<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
class UserController extends Controller {
 public function index(){return view('admin.users.index',['users'=>User::with('groups')->orderBy('name')->paginate(40)]);}
 public function edit(User $user){return view('admin.users.form',['user'=>$user,'groups'=>Group::orderBy('name')->get()]);}
 public function update(Request $r,User $user){$d=$r->validate(['name'=>'required|max:255','email'=>'required|email|max:255|unique:users,email,'.$user->id,'role'=>'required|in:user,student,teacher,editor,admin']);$user->update($d);$user->groups()->sync($r->input('group_ids',[]));return redirect()->route('admin.users.index')->with('success','Пользователь обновлён');}
}
