<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Group extends Model {
 protected $fillable=['name','code','curator_id','is_active'];
 protected $casts=['is_active'=>'boolean'];
 public function curator(){return $this->belongsTo(User::class,'curator_id');}
 public function users(){return $this->belongsToMany(User::class);}
 public function courses(){return $this->belongsToMany(Course::class);}
}
