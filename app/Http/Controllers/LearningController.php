<?php
namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\CourseCompletionService;
use App\Services\LessonCompletionService;
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

    public function lesson(Request $request, Lesson $lesson, LessonCompletionService $lessonCompletion)
    {
        $this->authorizeLesson($request,$lesson);
        $lesson->load(['course','material','scormPackages','files','links']);

        $lessonProgress = LessonProgress::firstOrCreate(
            ['lesson_id'=>$lesson->id,'user_id'=>$request->user()->id],
            ['status'=>'in_progress','started_at'=>now()]
        );
        if ($lessonProgress->status === 'not_started') {
            $lessonProgress->update(['status'=>'in_progress','started_at'=>now()]);
        }

        $lessonProgress = $lessonCompletion->refresh($lesson,$request->user()->id);
        $resourceState = $lessonCompletion->state($lesson,$request->user()->id);
        $resourceProgress = collect($resourceState['items'])->keyBy(function($item){
            return $item['type'].':'.$item['id'];
        });

        return view('learning.lesson',compact('lesson','lessonProgress','resourceState','resourceProgress'));
    }

    public function completeResource(Request $request, Lesson $lesson, string $type, int $resourceId, LessonCompletionService $lessonCompletion, CourseCompletionService $courseCompletion)
    {
        $this->authorizeLesson($request,$lesson);
        abort_unless(in_array($type,['material','file','link'],true),404);

        if ($type === 'material') {
            abort_unless((int)$lesson->material_id === $resourceId,404);
        } elseif ($type === 'file') {
            abort_unless($lesson->files()->whereKey($resourceId)->exists(),404);
        } elseif ($type === 'link') {
            abort_unless($lesson->links()->whereKey($resourceId)->exists(),404);
        }

        $lessonCompletion->markManual($lesson,$request->user()->id,$type,$resourceId);
        $lessonCompletion->refresh($lesson,$request->user()->id);
        $courseCompletion->refresh($lesson->course_id,$request->user()->id);

        return back()->with('success',__('ui.resource_done'));
    }

    public function complete(Request $request, Lesson $lesson, LessonCompletionService $lessonCompletion, CourseCompletionService $courseCompletion)
    {
        $this->authorizeLesson($request,$lesson);
        $state = $lessonCompletion->state($lesson,$request->user()->id);

        if ($state['required_count'] > 0) {
            $progress = $lessonCompletion->refresh($lesson,$request->user()->id);
            $courseCompletion->refresh($lesson->course_id,$request->user()->id);
            if (!$state['all_required_done']) {
                return back()->with('error',__('ui.finish_required_first'));
            }
            return back()->with('success',__('ui.lesson_completed'));
        }

        $progress = LessonProgress::firstOrCreate(['lesson_id'=>$lesson->id,'user_id'=>$request->user()->id]);
        $progress->update([
            'status'=>'completed',
            'started_at'=>$progress->started_at ?: now(),
            'completed_at'=>now()
        ]);
        $courseCompletion->refresh($lesson->course_id,$request->user()->id);
        return back()->with('success',__('ui.lesson_completed'));
    }

    private function authorizeLesson(Request $request, Lesson $lesson): void
    {
        abort_unless($lesson->is_active,404);
        $enrollment = Enrollment::where('course_id',$lesson->course_id)
            ->where('user_id',$request->user()->id)
            ->first();
        abort_unless($enrollment || $request->user()->isAdmin(),403);
    }
}
