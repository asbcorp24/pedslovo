<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Course extends Model {
 protected $fillable=['section_id','instructor_id','title','slug','description','study_year','pass_score','certificate_enabled','certificate_template_id','sort_order','is_active'];
 protected $casts=['is_active'=>'boolean','certificate_enabled'=>'boolean','pass_score'=>'decimal:2'];
 public function section(){return $this->belongsTo(Section::class);}
 public function instructor(){return $this->belongsTo(User::class,'instructor_id');}
 public function certificateTemplate(){return $this->belongsTo(CertificateTemplate::class);}
 public function lessons(){return $this->hasMany(Lesson::class)->orderBy('sort_order');}
 public function enrollments(){return $this->hasMany(Enrollment::class);}
 public function groups(){return $this->belongsToMany(Group::class);}
 public function certificates(){return $this->hasMany(Certificate::class);}
}
