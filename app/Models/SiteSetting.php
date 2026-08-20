<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SiteSetting extends Model{protected $fillable=['key','value','group'];public static function value(string $key,$default=null){$row=static::where('key',$key)->first();return $row?$row->value:$default;}public static function put(string $key,$value,string $group='site'){return static::updateOrCreate(['key'=>$key],['value'=>$value,'group'=>$group]);}}
