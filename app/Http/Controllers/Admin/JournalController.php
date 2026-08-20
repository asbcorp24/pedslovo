<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
class JournalController extends Controller {
 public function index(){return view('admin.journal.index',['courses'=>Course::withCount('enrollments')->orderBy('title')->get()]);}
 public function show(Course $course){$course->load(['lessons'=>fn($q)=>$q->where('is_active',true)->orderBy('sort_order')]);$enrollments=Enrollment::with('user')->where('course_id',$course->id)->orderBy('enrolled_at')->get();$progress=LessonProgress::whereIn('user_id',$enrollments->pluck('user_id'))->whereIn('lesson_id',$course->lessons->pluck('id'))->get()->keyBy(fn($p)=>$p->user_id.':'.$p->lesson_id);return view('admin.journal.show',compact('course','enrollments','progress'));}
}
