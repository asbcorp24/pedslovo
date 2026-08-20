<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\Course;use App\Models\Lesson;use App\Models\Section;use Illuminate\Http\Request;
class SortController extends Controller{public function update(Request $r,string $type){$d=$r->validate(['ids'=>'required|array','ids.*'=>'integer']);$model=match($type){'sections'=>Section::class,'courses'=>Course::class,'lessons'=>Lesson::class,default=>abort(404)};foreach($d['ids'] as $i=>$id)$model::whereKey($id)->update(['sort_order'=>$i+1]);return response()->json(['ok'=>true]);}}
