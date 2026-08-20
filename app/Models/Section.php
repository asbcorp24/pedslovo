<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id','title','slug','type','description','image','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function parent(){ return $this->belongsTo(self::class,'parent_id'); }
    public function children(){ return $this->hasMany(self::class,'parent_id')->orderBy('sort_order')->orderBy('title'); }
    public function materials(){ return $this->belongsToMany(Material::class)->withTimestamps(); }
    public function courses(){ return $this->hasMany(Course::class); }
    public function translations(){ return $this->hasMany(SectionTranslation::class); }
    public function scopeRoots($q){ return $q->whereNull('parent_id'); }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();
        return $translations->firstWhere('locale', $locale)
            ?: $translations->firstWhere('locale', 'ru');
    }

    public function localizedTitle(?string $locale = null): string
    {
        $translation = $this->translation($locale);
        return $translation && $translation->title ? $translation->title : $this->title;
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $translation = $this->translation($locale);
        if ($translation && $translation->description !== null && $translation->description !== '') {
            return $translation->description;
        }
        return $this->description;
    }
}
