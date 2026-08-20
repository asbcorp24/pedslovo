<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScormPackage extends Model {
 protected $fillable=['title','version','identifier','launch_path','storage_path','is_active'];
 protected $casts=['is_active'=>'boolean'];
 public function attempts(){ return $this->hasMany(ScormAttempt::class); }
}
