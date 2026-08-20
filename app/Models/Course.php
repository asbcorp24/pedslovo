<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Course extends Model {
 protected $fillable=['section_id','title','slug','description','study_year','sort_order','is_active'];
 protected $casts=['is_active'=>'boolean'];
 public function section(){return $this->belongsTo(Section::class);}
 public function lessons(){return $this->hasMany(Lesson::class)->orderBy('sort_order');}
 public function enrollments(){return $this->hasMany(Enrollment::class);}
}
