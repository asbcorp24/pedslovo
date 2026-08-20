<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SeoPage extends Model {
 protected $fillable=['name','path','title','description','keywords','og_title','og_description','og_image','canonical_url','robots','is_active'];
 protected $casts=['is_active'=>'boolean'];
}
