<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Certificate extends Model {
 protected $fillable=['course_id','user_id','certificate_template_id','number','verification_token','score','issued_at'];
 protected $casts=['issued_at'=>'datetime'];
 public function course(){return $this->belongsTo(Course::class);}
 public function user(){return $this->belongsTo(User::class);}
 public function template(){return $this->belongsTo(CertificateTemplate::class,'certificate_template_id');}
}
