<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Lesson extends Model {
 protected $fillable=['course_id','material_id','scorm_package_id','title','description','lesson_type','sort_order','is_required','is_active'];
 protected $casts=['is_required'=>'boolean','is_active'=>'boolean'];
 public function course(){return $this->belongsTo(Course::class);}
 public function material(){return $this->belongsTo(Material::class);}
 public function scormPackage(){return $this->belongsTo(ScormPackage::class);}
 public function progress(){return $this->hasMany(LessonProgress::class);}
}
