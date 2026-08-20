<?php
namespace App\Http\Controllers;
use App\Models\Course;use App\Models\Material;use App\Models\Section;
class SeoUtilityController extends Controller {
 public function sitemap(){ $urls=collect([[url('/'),now()]]); Section::where('is_active',true)->get()->each(fn($x)=>$urls->push([route('section.show',$x),$x->updated_at])); Course::where('is_active',true)->get()->each(fn($x)=>$urls->push([route('courses.show',$x),$x->updated_at])); Material::published()->get()->each(fn($x)=>$urls->push([route('material.show',$x),$x->updated_at])); return response()->view('seo.sitemap',compact('urls'))->header('Content-Type','application/xml'); }
 public function robots(){return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /scorm\nSitemap: ".url('/sitemap.xml')."\n",200)->header('Content-Type','text/plain');}
}
