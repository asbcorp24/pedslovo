<?php
namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\CourseCompletionService;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function myCourses(Request $request)
    {
        $enrollments = Enrollment::with(['course.lessons'])
            ->where('user_id',$request->user()->id)
            ->latest('enrolled_at')
            ->get();
        $progress = LessonProgress::where('user_id',$request->user()->id)->get()->keyBy('lesson_id');
        return view('learning.my-courses',compact('enrollments','progress'));
    }

    public function lesson(Request $request, Lesson $lesson)
    {
        abort_unless($lesson->is_active,404);
        $enrollment = Enrollment::where('course_id',$lesson->course_id)
            ->where('user_id',$request->user()->id)
            ->first();
        abort_unless($enrollment || $request->user()->isAdmin(),403);

        $lesson->load(['course','material','scormPackages','files','links']);
        $lessonProgress = LessonProgress::firstOrCreate(
            ['lesson_id'=>$lesson->id,'user_id'=>$request->user()->id],
            ['status'=>'in_progress','started_at'=>now()]
        );
        if ($lessonProgress->status === 'not_started') {
            $lessonProgress->update(['status'=>'in_progress','started_at'=>now()]);
        }
        return view('learning.lesson',compact('lesson','lessonProgress'));
    }

    public function complete(Request $request, Lesson $lesson, CourseCompletionService $completion)
    {
        $enrollment = Enrollment::where('course_id',$lesson->course_id)
            ->where('user_id',$request->user()->id)
            ->first();
        abort_unless($enrollment || $request->user()->isAdmin(),403);

        $progress = LessonProgress::firstOrCreate(['lesson_id'=>$lesson->id,'user_id'=>$request->user()->id]);
        $progress->update(['status'=>'completed','started_at'=>$progress->started_at ?: now(),'completed_at'=>now()]);
        $completion->refresh($lesson->course_id,$request->user()->id);
        return back()->with('success','Урок отмечен как пройденный');
    }
}
