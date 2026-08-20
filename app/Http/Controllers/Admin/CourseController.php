<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CourseController extends Controller {
 public function index(){return view('admin.courses.index',['courses'=>Course::with('section')->orderBy('sort_order')->paginate(30)]);}
 public function create(){return view('admin.courses.form',['course'=>new Course,'sections'=>Section::orderBy('title')->get()]);}
 public function store(Request $r){$data=$this->data($r);$data['slug']=$data['slug']?:Str::slug($data['title']).'-'.Str::lower(Str::random(5));Course::create($data);return redirect()->route('admin.courses.index')->with('success','Курс создан');}
 public function edit(Course $course){return view('admin.courses.form',compact('course')+['sections'=>Section::orderBy('title')->get()]);}
 public function update(Request $r,Course $course){$course->update($this->data($r));return redirect()->route('admin.courses.index')->with('success','Курс обновлён');}
 public function destroy(Course $course){$course->delete();return back()->with('success','Курс удалён');}
 private function data(Request $r){return $r->validate(['section_id'=>'nullable|exists:sections,id','title'=>'required|max:255','slug'=>'nullable|max:255','description'=>'nullable','study_year'=>'nullable|integer|min:1|max:4','sort_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean'])+['is_active'=>$r->boolean('is_active')];}
}
