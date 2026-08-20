<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    protected $fillable = ['section_id', 'locale', 'title', 'description'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
