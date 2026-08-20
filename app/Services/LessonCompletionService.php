<?php
namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonResourceProgress;
use App\Models\ScormAttempt;

class LessonCompletionService
{
    public function state(Lesson $lesson, int $userId): array
    {
        $lesson->loadMissing(['files','links','scormPackages']);
        $items = [];

        if ($lesson->material_id) {
            $items[] = [
                'type'=>'material',
                'id'=>(int)$lesson->material_id,
                'required'=>(bool)$lesson->material_required,
                'completed'=>$this->manualDone($lesson->id,$userId,'material',(int)$lesson->material_id),
            ];
        }

        foreach ($lesson->files as $file) {
            $items[] = [
                'type'=>'file',
                'id'=>(int)$file->id,
                'required'=>(bool)$file->is_required,
                'completed'=>$this->manualDone($lesson->id,$userId,'file',(int)$file->id),
            ];
        }

        foreach ($lesson->links as $link) {
            $items[] = [
                'type'=>'link',
                'id'=>(int)$link->id,
                'required'=>(bool)$link->is_required,
                'completed'=>$this->manualDone($lesson->id,$userId,'link',(int)$link->id),
            ];
        }

        foreach ($lesson->scormPackages as $package) {
            $done = ScormAttempt::where('lesson_id',$lesson->id)
                ->where('user_id',$userId)
                ->where('scorm_package_id',$package->id)
                ->whereIn('status',['completed','passed'])
                ->exists();
            $items[] = [
                'type'=>'scorm',
                'id'=>(int)$package->id,
                'required'=>(bool)$package->pivot->is_required,
                'completed'=>$done,
            ];
        }

        $required = array_values(array_filter($items,function($item){ return $item['required']; }));
        $allRequiredDone = count($required) === 0 || count(array_filter($required,function($item){ return $item['completed']; })) === count($required);

        return [
            'items'=>$items,
            'required_count'=>count($required),
            'required_done'=>count(array_filter($required,function($item){ return $item['completed']; })),
            'all_required_done'=>$allRequiredDone,
        ];
    }

    public function markManual(Lesson $lesson, int $userId, string $type, int $resourceId): void
    {
        LessonResourceProgress::updateOrCreate([
            'lesson_id'=>$lesson->id,
            'user_id'=>$userId,
            'resource_type'=>$type,
            'resource_id'=>$resourceId,
        ],[
            'completed_at'=>now(),
        ]);
    }

    public function refresh(Lesson $lesson, int $userId): LessonProgress
    {
        $state = $this->state($lesson,$userId);
        $progress = LessonProgress::firstOrCreate(
            ['lesson_id'=>$lesson->id,'user_id'=>$userId],
            ['status'=>'in_progress','started_at'=>now()]
        );

        if ($state['required_count'] > 0 && $state['all_required_done']) {
            $progress->update([
                'status'=>'completed',
                'started_at'=>$progress->started_at ?: now(),
                'completed_at'=>$progress->completed_at ?: now(),
            ]);
        } elseif ($state['required_count'] > 0 && in_array($progress->status,['completed','passed'],true)) {
            $progress->update(['status'=>'in_progress','completed_at'=>null]);
        }

        return $progress->fresh();
    }

    private function manualDone(int $lessonId, int $userId, string $type, int $resourceId): bool
    {
        return LessonResourceProgress::where('lesson_id',$lessonId)
            ->where('user_id',$userId)
            ->where('resource_type',$type)
            ->where('resource_id',$resourceId)
            ->whereNotNull('completed_at')
            ->exists();
    }
}
