<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ScormPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
class ScormController extends Controller {
 public function index(){return view('admin.scorm.index',['packages'=>ScormPackage::latest()->paginate(30)]);}
 public function store(Request $request){
  $request->validate(['title'=>'required|max:255','package'=>'required|file|mimes:zip|max:512000','max_attempts'=>'nullable|integer|min:1|max:100','pass_score'=>'nullable|numeric|min:0|max:100']);
  $uuid=(string)Str::uuid();$base='scorm/'.$uuid;Storage::disk('public')->makeDirectory($base);$tmp=$request->file('package')->getRealPath();$zip=new ZipArchive;abort_unless($zip->open($tmp)===true,422,'Не удалось открыть SCORM ZIP');
  for($i=0;$i<$zip->numFiles;$i++){$name=$zip->getNameIndex($i);if(str_contains($name,'..')||str_starts_with($name,'/')||preg_match('/^[A-Za-z]:[\\\\\/]/',$name)){$zip->close();Storage::disk('public')->deleteDirectory($base);abort(422,'Недопустимый путь внутри ZIP');}}
  $target=Storage::disk('public')->path($base);$zip->extractTo($target);$zip->close();$manifest=$target.'/imsmanifest.xml';if(!is_file($manifest)){Storage::disk('public')->deleteDirectory($base);abort(422,'В архиве нет imsmanifest.xml');}
  libxml_use_internal_errors(true);$xml=simplexml_load_file($manifest);if(!$xml){Storage::disk('public')->deleteDirectory($base);abort(422,'Некорректный imsmanifest.xml');}$resources=$xml->xpath('//*[local-name()="resource"]');$launch=null;$identifier=null;foreach($resources as $r){$attrs=$r->attributes();if(isset($attrs['href'])){$launch=(string)$attrs['href'];$identifier=(string)($attrs['identifier']??'');break;}}if(!$launch){Storage::disk('public')->deleteDirectory($base);abort(422,'Не найден запускаемый ресурс SCORM');}
  $raw=file_get_contents($manifest);$version=(str_contains($raw,'2004')||str_contains($raw,'adlcp_v1p3'))?'2004':'1.2';ScormPackage::create(['title'=>$request->title,'version'=>$version,'max_attempts'=>$request->max_attempts,'pass_score'=>$request->pass_score,'identifier'=>$identifier,'launch_path'=>$launch,'storage_path'=>$base,'is_active'=>true]);return back()->with('ok','SCORM-пакет iSpring загружен');
 }
 public function destroy(ScormPackage $scorm){Storage::disk('public')->deleteDirectory($scorm->storage_path);$scorm->delete();return back()->with('ok','SCORM-пакет удалён');}
}
