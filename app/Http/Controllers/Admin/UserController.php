<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Services\StudentCredentialCipher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::with('groups');
        if ($request->filled('q')) {
            $s = $request->q;
            $q->where(function($x) use ($s) {
                $x->where('name','like',"%$s%")->orWhere('email','like',"%$s%");
            });
        }
        if ($request->filled('role')) $q->where('role',$request->role);
        if ($request->filled('group_id')) $q->whereHas('groups',function($x) use ($request) {
            $x->where('groups.id',$request->group_id);
        });
        return view('admin.users.index',[
            'users'=>$q->orderBy('name')->paginate(40)->withQueryString(),
            'groups'=>Group::orderBy('name')->get()
        ]);
    }

    public function create(){ return view('admin.users.form',['user'=>new User,'groups'=>Group::orderBy('name')->get(),'visiblePassword'=>null]); }

    public function store(Request $request, StudentCredentialCipher $cipher)
    {
        $data = $this->data($request);
        $plain = $request->input('password') ?: Str::random(12);
        $data['password'] = Hash::make($plain);
        $data['student_password_secret'] = $data['role']==='student' ? $cipher->encrypt($plain) : null;
        $user = User::create($data);
        $user->groups()->sync($request->input('group_ids',[]));
        return redirect()->route('admin.users.edit',$user)->with('success','Пользователь создан. Пароль сохранён в зашифрованном виде.');
    }

    public function edit(User $user, StudentCredentialCipher $cipher)
    {
        $visiblePassword = null;
        if ($user->role === 'student' && $user->student_password_secret) {
            try { $visiblePassword = $cipher->decrypt($user->student_password_secret); } catch (Throwable $e) { $visiblePassword = null; }
        }
        return view('admin.users.form',['user'=>$user,'groups'=>Group::orderBy('name')->get(),'visiblePassword'=>$visiblePassword]);
    }

    public function update(Request $request, User $user, StudentCredentialCipher $cipher)
    {
        $data = $this->data($request,$user);
        if ($request->filled('password')) {
            $plain = (string)$request->password;
            $data['password'] = Hash::make($plain);
            $data['student_password_secret'] = $data['role']==='student' ? $cipher->encrypt($plain) : null;
        } elseif ($data['role'] !== 'student') {
            $data['student_password_secret'] = null;
        }
        $user->update($data);
        $user->groups()->sync($request->input('group_ids',[]));
        return redirect()->route('admin.users.edit',$user)->with('success','Пользователь обновлён');
    }

    public function credentials(Request $request, StudentCredentialCipher $cipher)
    {
        $q = User::with('groups')->where('role','student');
        if ($request->filled('q')) {
            $s = $request->q;
            $q->where(function($x) use ($s) {
                $x->where('name','like',"%$s%")->orWhere('email','like',"%$s%");
            });
        }
        if ($request->filled('group_id')) {
            $groupId = (int)$request->group_id;
            $q->whereHas('groups',function($x) use ($groupId) { $x->where('groups.id',$groupId); });
        }
        if ($request->filled('ids')) {
            $ids = collect(explode(',',(string)$request->ids))->map('intval')->filter()->unique()->values()->all();
            if ($ids) $q->whereIn('id',$ids);
        }

        $students = $q->orderBy('name')->get()->map(function(User $user) use ($cipher) {
            $plain = null;
            if ($user->student_password_secret) {
                try { $plain = $cipher->decrypt($user->student_password_secret); } catch (Throwable $e) { $plain = null; }
            }
            $user->setAttribute('visible_password',$plain);
            return $user;
        });

        return view('admin.users.credentials',[
            'students'=>$students,
            'groups'=>Group::orderBy('name')->get(),
            'selectedGroup'=>$request->group_id
        ]);
    }

    public function destroy(User $user)
    {
        abort_if($user->id===auth()->id(),422,'Нельзя удалить текущего пользователя');
        $user->delete();
        return back()->with('success','Пользователь удалён');
    }

    public function bulkGroup(Request $request)
    {
        $data=$request->validate(['user_ids'=>'required|array','user_ids.*'=>'exists:users,id','group_id'=>'required|exists:groups,id','mode'=>'required|in:add,move,remove']);
        $users=User::whereIn('id',$data['user_ids'])->get();
        foreach($users as $user){
            if($data['mode']==='move') $user->groups()->sync([$data['group_id']]);
            elseif($data['mode']==='remove') $user->groups()->detach($data['group_id']);
            else $user->groups()->syncWithoutDetaching([$data['group_id']]);
        }
        return back()->with('success','Групповая операция выполнена');
    }

    public function import(Request $request, StudentCredentialCipher $cipher)
    {
        $request->validate(['file'=>'required|file|mimes:csv,txt,xlsx','group_id'=>'nullable|exists:groups,id']);
        $rows=$this->rows($request->file('file')->getRealPath(),strtolower($request->file('file')->getClientOriginalExtension()));
        $count=0;
        foreach($rows as $row){
            $name=trim($row['name']??$row['fio']??'');
            $email=trim($row['email']??'');
            if(!$name||!filter_var($email,FILTER_VALIDATE_EMAIL)) continue;
            $role=in_array(($row['role']??'student'),['student','teacher','editor','admin'],true)?$row['role']:'student';
            $plain=(string)($row['password']??'');
            if($plain==='') $plain=Str::random(12);
            $values=['name'=>$name,'role'=>$role,'password'=>Hash::make($plain),'student_password_secret'=>$role==='student'?$cipher->encrypt($plain):null];
            $user=User::updateOrCreate(['email'=>$email],$values);
            if($request->group_id) $user->groups()->syncWithoutDetaching([$request->group_id]);
            $count++;
        }
        return back()->with('success','Импортировано пользователей: '.$count.'. Пароли студентов сохранены зашифрованно.');
    }

    private function data(Request $request, ?User $user=null)
    {
        return $request->validate([
            'name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users,email'.($user?','.$user->id:''),
            'role'=>'required|in:student,teacher,editor,admin',
            'password'=>'nullable|min:8'
        ]);
    }

    private function rows($path,$ext){ return $ext==='xlsx'?$this->xlsx($path):$this->csv($path); }
    private function csv($path){$h=fopen($path,'r');$first=fgets($h);rewind($h);$delimiter=substr_count($first,';')>substr_count($first,',')?';':',';$headers=array_map(function($v){return strtolower(trim($v));},fgetcsv($h,0,$delimiter));$rows=[];while(($r=fgetcsv($h,0,$delimiter))!==false){$r=array_pad($r,count($headers),null);$rows[]=array_combine($headers,array_slice($r,0,count($headers)));}fclose($h);return $rows;}
    private function xlsx($path){$zip=new ZipArchive;if($zip->open($path)!==true)return[];$shared=[];$s=$zip->getFromName('xl/sharedStrings.xml');if($s){$xml=simplexml_load_string($s);foreach($xml->si as $si)$shared[]=trim((string)$si->t ?: implode('',array_map(function($x){return (string)$x->t;},iterator_to_array($si->r))));}$sheet=$zip->getFromName('xl/worksheets/sheet1.xml');$zip->close();if(!$sheet)return[];$xml=simplexml_load_string($sheet);$rows=[];$headers=[];foreach($xml->sheetData->row as $row){$vals=[];foreach($row->c as $c){$v=(string)$c->v;$vals[]=(string)$c['t']==='s'?($shared[(int)$v]??''):$v;}if(!$headers){$headers=array_map(function($v){return strtolower(trim($v));},$vals);continue;}$vals=array_pad($vals,count($headers),null);$rows[]=array_combine($headers,array_slice($vals,0,count($headers)));}return $rows;}
}
