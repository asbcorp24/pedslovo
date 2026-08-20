<?php
namespace App\Http\Controllers;
use App\Models\Section;
use App\Models\Material;
class HomeController extends Controller { public function index(){ $sections=Section::roots()->where('is_active',true)->with(['children'=>fn($q)=>$q->where('is_active',true)])->orderBy('sort_order')->get(); $latest=Material::published()->latest('published_at')->limit(8)->get(); return view('home',compact('sections','latest')); } }
