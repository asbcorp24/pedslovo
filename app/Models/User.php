<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use HasFactory,Notifiable;
 protected $fillable=['name','email','password','role'];
 protected $hidden=['password','remember_token'];
 protected $casts=['email_verified_at'=>'datetime'];
 public function isAdmin(){return in_array($this->role,['admin','editor']);}
 public function groups(){return $this->belongsToMany(Group::class);}
 public function taughtCourses(){return $this->hasMany(Course::class,'instructor_id');}
 public function certificates(){return $this->hasMany(Certificate::class);}
}
