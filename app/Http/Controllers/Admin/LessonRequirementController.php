<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonRequirementController extends Controller
{
    public function edit(Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        $lesson->load(['material','files','links','scormPackages']);
        return view('admin.lessons.requirements',compact('course','lesson'));
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        $data = $request->validate([
            'material_required'=>'nullable|boolean',
            'required_file_ids'=>'nullable|array',
            'required_file_ids.*'=>'integer',
            'required_link_ids'=>'nullable|array',
            'required_link_ids.*'=>'integer',
            'required_scorm_ids'=>'nullable|array',
            'required_scorm_ids.*'=>'integer',
        ]);

        $lesson->update(['material_required'=>$request->boolean('material_required')]);

        $requiredFiles = array_map('intval',$data['required_file_ids'] ?? []);
        foreach ($lesson->files as $file) {
            $file->update(['is_required'=>in_array((int)$file->id,$requiredFiles,true)]);
        }

        $requiredLinks = array_map('intval',$data['required_link_ids'] ?? []);
        foreach ($lesson->links as $link) {
            $link->update(['is_required'=>in_array((int)$link->id,$requiredLinks,true)]);
        }

        $requiredScorm = array_map('intval',$data['required_scorm_ids'] ?? []);
        foreach ($lesson->scormPackages as $package) {
            $lesson->scormPackages()->updateExistingPivot($package->id,[
                'is_required'=>in_array((int)$package->id,$requiredScorm,true)
            ]);
        }

        return back()->with('success','Обязательность элементов урока сохранена');
    }
}
