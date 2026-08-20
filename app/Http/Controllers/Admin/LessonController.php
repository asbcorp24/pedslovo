<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\ScormPackage;
use Illuminate\Http\Request;
class LessonController extends Controller {
 public function index(Course $course){$course->load(['lessons.material','lessons.scormPackage']);return view('admin.lessons.index',compact('course'));}
 public function create(Course $course){return view('admin.lessons.form',['course'=>$course,'lesson'=>new Lesson,'materials'=>Material::orderBy('title')->get(),'scormPackages'=>ScormPackage::where('is_active',true)->orderBy('title')->get()]);}
 public function store(Request $r,Course $course){$data=$this->data($r);$data['course_id']=$course->id;Lesson::create($data);return redirect()->route('admin.courses.lessons.index',$course)->with('success','Урок добавлен');}
 public function edit(Course $course,Lesson $lesson){abort_unless($lesson->course_id===$course->id,404);return view('admin.lessons.form',compact('course','lesson')+['materials'=>Material::orderBy('title')->get(),'scormPackages'=>ScormPackage::where('is_active',true)->orderBy('title')->get()]);}
 public function update(Request $r,Course $course,Lesson $lesson){abort_unless($lesson->course_id===$course->id,404);$lesson->update($this->data($r));return redirect()->route('admin.courses.lessons.index',$course)->with('success','Урок обновлён');}
 public function destroy(Course $course,Lesson $lesson){abort_unless($lesson->course_id===$course->id,404);$lesson->delete();return back()->with('success','Урок удалён');}
 private function data(Request $r){$data=$r->validate(['title'=>'required|max:255','description'=>'nullable','lesson_type'=>'required|in:material,scorm,video,audio,pdf,text','material_id'=>'nullable|exists:materials,id','scorm_package_id'=>'nullable|exists:scorm_packages,id','sort_order'=>'nullable|integer|min:0','is_required'=>'nullable|boolean','is_active'=>'nullable|boolean']);$data['is_required']=$r->boolean('is_required');$data['is_active']=$r->boolean('is_active');if($data['lesson_type']==='scorm')$data['material_id']=null;else $data['scorm_package_id']=null;return $data;}
}
