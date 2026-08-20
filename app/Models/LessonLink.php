<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonLink extends Model
{
    protected $fillable = ['lesson_id','title','provider','url','embed_url','sort_order'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
