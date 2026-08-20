<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonFile extends Model
{
    protected $fillable = [
        'lesson_id','original_name','path','mime_type','size','is_primary','launch_path'
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getLaunchUrlAttribute()
    {
        if (!$this->launch_path) {
            return null;
        }
        return Storage::disk('public')->url($this->launch_path);
    }
}
