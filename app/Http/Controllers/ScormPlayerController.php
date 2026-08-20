<?php
namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\ScormAttempt;
use App\Models\ScormPackage;
use App\Services\CourseCompletionService;
use App\Services\LessonCompletionService;
use Illuminate\Http\Request;

class ScormPlayerController extends Controller
{
    public function launch(Request $request, ScormPackage $scorm)
    {
        abort_unless($scorm->is_active,404);
        $lesson = null;

        if ($request->filled('lesson')) {
            $lesson = Lesson::findOrFail((int)$request->input('lesson'));
            $linked = $lesson->scormPackages()->where('scorm_packages.id',$scorm->id)->exists()
                || (int)$lesson->scorm_package_id === (int)$scorm->id;
            abort_unless($linked,404);

            $enrollment = Enrollment::where('course_id',$lesson->course_id)
                ->where('user_id',auth()->id())
                ->exists();
            abort_unless($enrollment || auth()->user()->isAdmin(),403);
        }

        if ($scorm->max_attempts && !auth()->user()->isAdmin()) {
            $count = ScormAttempt::where('scorm_package_id',$scorm->id)
                ->where('user_id',auth()->id())
                ->when($lesson,function($q) use ($lesson){ return $q->where('lesson_id',$lesson->id); })
                ->count();
            abort_if($count >= $scorm->max_attempts,403,'Количество попыток исчерпано');
        }

        $attempt = ScormAttempt::create([
            'scorm_package_id'=>$scorm->id,
            'lesson_id'=>$lesson ? $lesson->id : null,
            'user_id'=>auth()->id(),
            'status'=>'incomplete',
            'started_at'=>now(),
            'cmi_data'=>[]
        ]);

        return view('scorm.player',compact('scorm','attempt'));
    }

    public function state(ScormAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        return response()->json([
            'ok'=>true,
            'data'=>$attempt->cmi_data ?: [],
            'status'=>$attempt->status,
            'score_raw'=>$attempt->score_raw,
            'lesson_location'=>$attempt->lesson_location,
            'suspend_data'=>$attempt->suspend_data
        ]);
    }

    public function commit(Request $request, ScormAttempt $attempt, LessonCompletionService $lessonCompletion, CourseCompletionService $courseCompletion)
    {
        $this->authorizeAttempt($attempt);
        $data = $request->validate(['data'=>'required|array']);
        $cmi = $attempt->cmi_data ?: [];
        foreach ($data['data'] as $key=>$value) {
            $cmi[(string)$key] = is_scalar($value) || is_null($value) ? (string)$value : $value;
        }

        $status = isset($cmi['cmi.core.lesson_status']) ? $cmi['cmi.core.lesson_status'] : (isset($cmi['cmi.completion_status']) ? $cmi['cmi.completion_status'] : $attempt->status);
        $success = isset($cmi['cmi.success_status']) ? $cmi['cmi.success_status'] : null;
        $score = isset($cmi['cmi.core.score.raw']) ? $cmi['cmi.core.score.raw'] : (isset($cmi['cmi.score.raw']) ? $cmi['cmi.score.raw'] : $attempt->score_raw);

        if ($success === 'passed') $status = 'passed';
        elseif ($success === 'failed') $status = 'failed';
        elseif (is_numeric($score) && $attempt->package->pass_score !== null) $status = ((float)$score >= (float)$attempt->package->pass_score) ? 'passed' : 'failed';

        $attempt->update([
            'cmi_data'=>$cmi,
            'status'=>$status ?: $attempt->status,
            'score_raw'=>is_numeric($score) ? $score : $attempt->score_raw,
            'score_min'=>is_numeric(isset($cmi['cmi.core.score.min']) ? $cmi['cmi.core.score.min'] : null) ? $cmi['cmi.core.score.min'] : $attempt->score_min,
            'score_max'=>is_numeric(isset($cmi['cmi.core.score.max']) ? $cmi['cmi.core.score.max'] : null) ? $cmi['cmi.core.score.max'] : $attempt->score_max,
            'lesson_location'=>isset($cmi['cmi.core.lesson_location']) ? $cmi['cmi.core.lesson_location'] : (isset($cmi['cmi.location']) ? $cmi['cmi.location'] : $attempt->lesson_location),
            'suspend_data'=>isset($cmi['cmi.suspend_data']) ? $cmi['cmi.suspend_data'] : $attempt->suspend_data,
            'session_time'=>isset($cmi['cmi.core.session_time']) ? $cmi['cmi.core.session_time'] : (isset($cmi['cmi.session_time']) ? $cmi['cmi.session_time'] : $attempt->session_time),
            'completed_at'=>in_array($status,['completed','passed','failed'],true) ? now() : $attempt->completed_at
        ]);

        if ($attempt->lesson_id) {
            $lesson = Lesson::find($attempt->lesson_id);
            if ($lesson) {
                $progress = LessonProgress::firstOrCreate(
                    ['lesson_id'=>$lesson->id,'user_id'=>$attempt->user_id],
                    ['status'=>'in_progress','started_at'=>$attempt->started_at]
                );
                if (is_numeric($score)) {
                    $progress->update(['score'=>$score,'started_at'=>$progress->started_at ?: $attempt->started_at]);
                }
                $lessonCompletion->refresh($lesson,$attempt->user_id);
                $courseCompletion->refresh($lesson->course_id,$attempt->user_id);
            }
        }

        return response()->json(['ok'=>true]);
    }

    private function authorizeAttempt(ScormAttempt $attempt)
    {
        abort_unless(auth()->check() && ((int)$attempt->user_id === (int)auth()->id() || auth()->user()->isAdmin()),403);
    }
}
