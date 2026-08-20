<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','student_password_secret','role','approved_at','registration_requested_at'];
    protected $hidden = ['password','student_password_secret','remember_token'];
    protected $casts = [
        'email_verified_at'=>'datetime',
        'approved_at'=>'datetime',
        'registration_requested_at'=>'datetime',
    ];

    public function isAdmin(){ return $this->role==='admin'; }
    public function canEditContent(){ return in_array($this->role,['admin','editor'],true); }
    public function isTeacher(){ return $this->role==='teacher'; }
    public function isApproved(){ return $this->approved_at !== null; }
    public function groups(){ return $this->belongsToMany(Group::class); }
    public function taughtCourses(){ return $this->hasMany(Course::class,'instructor_id'); }
    public function certificates(){ return $this->hasMany(Certificate::class); }
    public function scormAttempts(){ return $this->hasMany(ScormAttempt::class); }
}
