<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScormPackage extends Model {
 protected $fillable=['title','version','max_attempts','pass_score','identifier','launch_path','storage_path','is_active'];
 protected $casts=['is_active'=>'boolean','pass_score'=>'decimal:2'];
 public function attempts(){return $this->hasMany(ScormAttempt::class);}
}
