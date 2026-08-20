<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScormAttempt extends Model {
 protected $fillable=['scorm_package_id','lesson_id','user_id','status','score_raw','score_min','score_max','lesson_location','suspend_data','session_time','total_time','cmi_data','started_at','completed_at'];
 protected $casts=['cmi_data'=>'array','started_at'=>'datetime','completed_at'=>'datetime'];
 public function package(){return $this->belongsTo(ScormPackage::class,'scorm_package_id');}
 public function lesson(){return $this->belongsTo(Lesson::class);}
 public function user(){return $this->belongsTo(User::class);}
}
