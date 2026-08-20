<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
class GroupController extends Controller {
 public function index(){return view('admin.groups.index',['groups'=>Group::withCount(['users','courses'])->with('curator')->orderBy('name')->paginate(30)]);}
 public function create(){return view('admin.groups.form',['group'=>new Group,'users'=>User::orderBy('name')->get(),'courses'=>Course::orderBy('title')->get()]);}
 public function store(Request $r){$g=Group::create($this->data($r));$this->sync($r,$g);return redirect()->route('admin.groups.index')->with('success','Группа создана');}
 public function edit(Group $group){return view('admin.groups.form',['group'=>$group,'users'=>User::orderBy('name')->get(),'courses'=>Course::orderBy('title')->get()]);}
 public function update(Request $r,Group $group){$group->update($this->data($r));$this->sync($r,$group);return redirect()->route('admin.groups.index')->with('success','Группа обновлена');}
 public function destroy(Group $group){$group->delete();return back()->with('success','Группа удалена');}
 private function data(Request $r){$d=$r->validate(['name'=>'required|max:255','code'=>'nullable|max:100','curator_id'=>'nullable|exists:users,id','is_active'=>'nullable|boolean']);$d['is_active']=$r->boolean('is_active');return $d;}
 private function sync(Request $r,Group $g){$g->users()->sync($r->input('user_ids',[]));$g->courses()->sync($r->input('course_ids',[]));foreach($g->courses as $course){foreach($g->users as $u){$course->enrollments()->firstOrCreate(['user_id'=>$u->id],['status'=>'active','enrolled_at'=>now()]);}}}
}
