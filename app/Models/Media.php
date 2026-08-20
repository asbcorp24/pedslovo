<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class Media extends Model{protected $table='media_library';protected $fillable=['user_id','name','disk','path','mime','size','alt'];public function user(){return $this->belongsTo(User::class);}public function getUrlAttribute(){return \Storage::disk($this->disk)->url($this->path);}}
