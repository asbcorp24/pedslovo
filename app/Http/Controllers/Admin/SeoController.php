<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use Illuminate\Http\Request;
class SeoController extends Controller {
 public function index(){return view('admin.seo.index',['pages'=>SeoPage::orderBy('path')->paginate(40)]);}
 public function create(){return view('admin.seo.form',['page'=>new SeoPage]);}
 public function store(Request $r){SeoPage::create($this->data($r));return redirect()->route('admin.seo.index')->with('ok','SEO-настройка создана');}
 public function edit(SeoPage $seo){return view('admin.seo.form',['page'=>$seo]);}
 public function update(Request $r,SeoPage $seo){$seo->update($this->data($r,$seo));return redirect()->route('admin.seo.index')->with('ok','SEO-настройка обновлена');}
 public function destroy(SeoPage $seo){$seo->delete();return back()->with('ok','SEO-настройка удалена');}
 private function data(Request $r,?SeoPage $page=null){return $r->validate(['name'=>'required|max:255','path'=>'required|max:255|unique:seo_pages,path'.($page?','.$page->id:''),'title'=>'nullable|max:255','description'=>'nullable|max:1000','keywords'=>'nullable|max:2000','og_title'=>'nullable|max:255','og_description'=>'nullable|max:1000','og_image'=>'nullable|max:2048','canonical_url'=>'nullable|max:2048','robots'=>'required|max:80','is_active'=>'nullable|boolean'])+['is_active'=>$r->boolean('is_active')];}
}
