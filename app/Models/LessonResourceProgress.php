<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonResourceProgress extends Model
{
    protected $table = 'lesson_resource_progress';

    protected $fillable = [
        'lesson_id','user_id','resource_type','resource_id','completed_at'
    ];

    protected $casts = ['completed_at'=>'datetime'];

    public function lesson(){ return $this->belongsTo(Lesson::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
