<?php
namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
class CourseController extends Controller {
 public function show(Course $course){$course->load(['section','lessons.material','lessons.scormPackage']);$enrollment=auth()->check()?Enrollment::where('course_id',$course->id)->where('user_id',auth()->id())->first():null;return view('courses.show',compact('course','enrollment'));}
 public function enroll(Request $request,Course $course){Enrollment::firstOrCreate(['course_id'=>$course->id,'user_id'=>$request->user()->id],['status'=>'active','enrolled_at'=>now()]);return back()->with('success','Вы записаны на курс');}
}
