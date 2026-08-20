<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id','material_id','scorm_package_id','title','description','lesson_type',
        'sort_order','is_required','is_active'
    ];

    protected $casts = ['is_required'=>'boolean','is_active'=>'boolean'];

    public function course(){ return $this->belongsTo(Course::class); }
    public function material(){ return $this->belongsTo(Material::class); }

    // Старое поле оставляем для совместимости со старыми данными.
    public function scormPackage(){ return $this->belongsTo(ScormPackage::class); }

    // В одном уроке может быть несколько SCORM-тестов/модулей.
    public function scormPackages()
    {
        return $this->belongsToMany(ScormPackage::class,'lesson_scorm_package')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('lesson_scorm_package.sort_order')
            ->orderBy('scorm_packages.title');
    }

    public function progress(){ return $this->hasMany(LessonProgress::class); }
    public function files(){ return $this->hasMany(LessonFile::class)->orderByDesc('is_primary')->orderBy('id'); }
    public function primaryFile(){ return $this->hasOne(LessonFile::class)->where('is_primary',true); }
}
