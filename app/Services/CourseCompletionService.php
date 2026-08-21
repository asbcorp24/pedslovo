<?php
namespace App\Services;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Support\Str;
class CourseCompletionService {
 public function refresh(int $courseId,int $userId): void {
  $course=Course::with(['lessons'=>fn($q)=>$q->where('is_active',true)->where('is_required',true),'certificateTemplate'])->find($courseId);if(!$course)return;
  $enrollment=Enrollment::where('course_id',$courseId)->where('user_id',$userId)->first();if(!$enrollment||$course->lessons->isEmpty())return;
  $progress=LessonProgress::where('user_id',$userId)->whereIn('lesson_id',$course->lessons->pluck('id'))->get();
  $passed=$progress->whereIn('status',['completed','passed'])->count();if($passed!==$course->lessons->count())return;
  if($course->pass_score!==null){$scores=$progress->whereNotNull('score')->pluck('score');$avg=$scores->count()?$scores->avg():null;if($avg===null||$avg<(float)$course->pass_score)return;}else{$avg=$progress->whereNotNull('score')->pluck('score')->avg();}
  $enrollment->update(['status'=>'completed','completed_at'=>$enrollment->completed_at?:now()]);
  if($course->certificate_enabled){Certificate::firstOrCreate(['course_id'=>$courseId,'user_id'=>$userId],['certificate_template_id'=>$course->certificate_template_id,'number'=>'PED-'.now()->format('Ymd').'-'.strtoupper(Str::random(8)),'verification_token'=>Str::random(48),'score'=>$avg,'issued_at'=>now()]);}
 }
}
