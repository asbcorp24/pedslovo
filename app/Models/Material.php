<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Material extends Model { use HasFactory; protected $fillable=['title','slug','annotation','content','material_type','author','cover','file_path','media_url','status','published_at']; protected $casts=['published_at'=>'datetime']; public function sections(){ return $this->belongsToMany(Section::class)->withTimestamps(); } public function scopePublished($q){ return $q->where('status','published')->where(function($x){ $x->whereNull('published_at')->orWhere('published_at','<=',now()); }); } }
