<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id','material_id','material_required','scorm_package_id','title','description','lesson_type',
        'sort_order','is_required','is_active'
    ];

    protected $casts = [
        'material_required'=>'boolean',
        'is_required'=>'boolean',
        'is_active'=>'boolean'
    ];

    public function course(){ return $this->belongsTo(Course::class); }
    public function material(){ return $this->belongsTo(Material::class); }
    public function scormPackage(){ return $this->belongsTo(ScormPackage::class); }

    public function scormPackages()
    {
        return $this->belongsToMany(ScormPackage::class,'lesson_scorm_package')
            ->withPivot(['sort_order','is_required'])
            ->withTimestamps()
            ->orderBy('lesson_scorm_package.sort_order')
            ->orderBy('scorm_packages.title');
    }

    public function progress(){ return $this->hasMany(LessonProgress::class); }
    public function resourceProgress(){ return $this->hasMany(LessonResourceProgress::class); }
    public function files(){ return $this->hasMany(LessonFile::class)->orderByDesc('is_primary')->orderBy('id'); }
    public function primaryFile(){ return $this->hasOne(LessonFile::class)->where('is_primary',true); }
    public function links(){ return $this->hasMany(LessonLink::class)->orderBy('sort_order')->orderBy('id'); }
}
