<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonFile;
use App\Models\Material;
use App\Models\ScormPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $course->load(['lessons.material','lessons.scormPackages','lessons.files','lessons.links']);
        return view('admin.lessons.index',compact('course'));
    }

    public function create(Course $course)
    {
        return view('admin.lessons.form',[
            'course'=>$course,
            'lesson'=>new Lesson,
            'materials'=>Material::orderBy('title')->get(),
            'scormPackages'=>ScormPackage::where('is_active',true)->orderBy('title')->get()
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $data = $this->data($request);
        $data['course_id'] = $course->id;
        $lesson = Lesson::create($data);

        $this->syncExistingScorm($request,$lesson);
        $this->storeFiles($request,$lesson);
        $this->storeScormFiles($request,$lesson);
        $this->syncVideoLinks($request,$lesson);

        return redirect()->route('admin.courses.lessons.index',$course)->with('success','Урок добавлен');
    }

    public function edit(Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        $lesson->load(['files','scormPackages','links']);
        return view('admin.lessons.form',[
            'course'=>$course,
            'lesson'=>$lesson,
            'materials'=>Material::orderBy('title')->get(),
            'scormPackages'=>ScormPackage::where('is_active',true)->orderBy('title')->get()
        ]);
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        $lesson->update($this->data($request));

        $this->syncExistingScorm($request,$lesson);
        $this->storeFiles($request,$lesson);
        $this->storeScormFiles($request,$lesson);
        $this->syncVideoLinks($request,$lesson);

        return redirect()->route('admin.courses.lessons.index',$course)->with('success','Урок обновлён');
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        Storage::disk('public')->deleteDirectory('lessons/'.$lesson->id);
        $lesson->delete();
        return back()->with('success','Урок удалён');
    }

    public function destroyFile(Course $course, Lesson $lesson, LessonFile $file)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id && (int)$file->lesson_id === (int)$lesson->id,404);
        if ($file->launch_path && Str::contains($file->launch_path, '/archive/')) {
            $archiveDir = Str::beforeLast($file->launch_path, '/');
            Storage::disk('public')->deleteDirectory($archiveDir);
        }
        Storage::disk('public')->delete($file->path);
        $wasPrimary = $file->is_primary;
        $file->delete();
        if ($wasPrimary) {
            $next = $lesson->files()->first();
            if ($next) $next->update(['is_primary'=>true]);
        }
        return back()->with('success','Файл урока удалён');
    }

    private function data(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|max:255',
            'description'=>'nullable',
            'lesson_type'=>'required|in:mixed,material,scorm,pdf,html,archive,file,video,audio,text',
            'material_id'=>'nullable|exists:materials,id',
            'scorm_package_ids'=>'nullable|array',
            'scorm_package_ids.*'=>'integer|exists:scorm_packages,id',
            'sort_order'=>'nullable|integer|min:0',
            'is_required'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
            'files'=>'nullable|array',
            'files.*'=>'file|max:102400',
            'scorm_files'=>'nullable|array',
            'scorm_files.*'=>'file|mimes:zip|max:512000',
            'scorm_max_attempts'=>'nullable|integer|min:1|max:100',
            'scorm_pass_score'=>'nullable|numeric|min:0|max:100',
            'video_links'=>'nullable|string|max:20000'
        ]);

        unset($data['files'],$data['scorm_files'],$data['scorm_package_ids'],$data['scorm_max_attempts'],$data['scorm_pass_score'],$data['video_links']);
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function syncVideoLinks(Request $request, Lesson $lesson)
    {
        $raw = trim((string)$request->input('video_links',''));
        $lesson->links()->delete();
        if ($raw === '') return;

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $sort = 0;
        foreach ($lines as $line) {
            $url = trim($line);
            if ($url === '') continue;
            if (!filter_var($url,FILTER_VALIDATE_URL)) {
                abort(422,'Некорректная ссылка на видео: '.$url);
            }
            $parsed = $this->parseVideoUrl($url);
            $lesson->links()->create([
                'title'=>$parsed['title'],
                'provider'=>$parsed['provider'],
                'url'=>$url,
                'embed_url'=>$parsed['embed_url'],
                'sort_order'=>$sort++
            ]);
        }
    }

    private function parseVideoUrl($url)
    {
        $host = strtolower((string)parse_url($url,PHP_URL_HOST));
        $path = trim((string)parse_url($url,PHP_URL_PATH),'/');
        $query = [];
        parse_str((string)parse_url($url,PHP_URL_QUERY),$query);

        if (in_array($host,['youtube.com','www.youtube.com','m.youtube.com','youtu.be'],true)) {
            $id = null;
            if ($host === 'youtu.be') $id = explode('/',$path)[0] ?? null;
            elseif (!empty($query['v'])) $id = $query['v'];
            elseif (preg_match('~^(?:shorts|embed|live)/([^/]+)~',$path,$m)) $id = $m[1];
            if ($id && preg_match('/^[A-Za-z0-9_-]{6,20}$/',$id)) {
                return ['provider'=>'youtube','title'=>'YouTube','embed_url'=>'https://www.youtube.com/embed/'.$id];
            }
        }

        if (in_array($host,['rutube.ru','www.rutube.ru'],true)) {
            $id = null;
            if (preg_match('~^(?:video|play/embed)/([A-Za-z0-9_-]+)~',$path,$m)) $id = $m[1];
            if ($id) {
                return ['provider'=>'rutube','title'=>'Rutube','embed_url'=>'https://rutube.ru/play/embed/'.$id];
            }
        }

        return ['provider'=>'external','title'=>'Внешнее видео','embed_url'=>null];
    }

    private function syncExistingScorm(Request $request, Lesson $lesson)
    {
        $ids = collect($request->input('scorm_package_ids',[]))->map(function($id){ return (int)$id; })->filter()->unique()->values()->all();
        $lesson->scormPackages()->sync($ids);
        $lesson->update(['scorm_package_id'=>$ids ? $ids[0] : null]);
    }

    private function storeScormFiles(Request $request, Lesson $lesson)
    {
        if (!$request->hasFile('scorm_files')) return;

        foreach ($request->file('scorm_files') as $upload) {
            $package = $this->createScormPackageFromUpload($upload,$lesson,$request);
            $lesson->scormPackages()->syncWithoutDetaching([$package->id]);
            if (!$lesson->scorm_package_id) $lesson->update(['scorm_package_id'=>$package->id]);
        }
    }

    private function createScormPackageFromUpload($upload, Lesson $lesson, Request $request)
    {
        $uuid = (string) Str::uuid();
        $base = 'lessons/'.$lesson->id.'/scorm/'.$uuid;
        $content = $base.'/content';
        Storage::disk('public')->makeDirectory($content);

        $zipStored = $upload->storeAs($base,'package.zip','public');
        $zipPath = Storage::disk('public')->path($zipStored);
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            Storage::disk('public')->deleteDirectory($base);
            abort(422,'Не удалось открыть SCORM ZIP: '.$upload->getClientOriginalName());
        }

        for ($i=0; $i<$zip->numFiles; $i++) {
            $name = str_replace('\\','/',$zip->getNameIndex($i));
            if ($name === '' || Str::startsWith($name,'/') || Str::contains($name,'../') || preg_match('/^[A-Za-z]:[\\\\\/]/',$name)) {
                $zip->close();
                Storage::disk('public')->deleteDirectory($base);
                abort(422,'Недопустимый путь внутри SCORM ZIP');
            }
            if (Str::endsWith($name,'/')) continue;
            $ext = strtolower(pathinfo($name,PATHINFO_EXTENSION));
            if (in_array($ext,['php','php3','php4','php5','phtml','phar','cgi','pl','py','sh','bat','cmd','exe','com'],true)) {
                $zip->close();
                Storage::disk('public')->deleteDirectory($base);
                abort(422,'SCORM содержит недопустимый исполняемый файл: '.$name);
            }
        }

        $target = Storage::disk('public')->path($content);
        $zip->extractTo($target);
        $zip->close();

        $manifest = $this->findManifest($target);
        if (!$manifest) {
            Storage::disk('public')->deleteDirectory($base);
            abort(422,'В SCORM-архиве нет imsmanifest.xml');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($manifest);
        if (!$xml) {
            Storage::disk('public')->deleteDirectory($base);
            abort(422,'Некорректный imsmanifest.xml');
        }

        $resources = $xml->xpath('//*[local-name()="resource"]');
        $launch = null;
        $identifier = null;
        foreach ($resources as $resource) {
            $attrs = $resource->attributes();
            if (isset($attrs['href'])) {
                $launch = (string)$attrs['href'];
                $identifier = isset($attrs['identifier']) ? (string)$attrs['identifier'] : null;
                break;
            }
        }

        if (!$launch) {
            Storage::disk('public')->deleteDirectory($base);
            abort(422,'Не найден запускаемый ресурс SCORM');
        }

        $manifestDir = dirname($manifest);
        $relativeManifestDir = ltrim(str_replace(str_replace('\\','/',$target),'',str_replace('\\','/',$manifestDir)),'/');
        $launch = ($relativeManifestDir ? $relativeManifestDir.'/' : '').ltrim(str_replace('\\','/',$launch),'/');

        $raw = file_get_contents($manifest);
        $version = (strpos($raw,'2004') !== false || strpos($raw,'adlcp_v1p3') !== false) ? '2004' : '1.2';

        return ScormPackage::create([
            'title'=>$lesson->title.' — '.$upload->getClientOriginalName(),
            'version'=>$version,
            'max_attempts'=>$request->input('scorm_max_attempts'),
            'pass_score'=>$request->input('scorm_pass_score'),
            'identifier'=>$identifier,
            'launch_path'=>$launch,
            'storage_path'=>$content,
            'is_active'=>true
        ]);
    }

    private function findManifest($target)
    {
        $direct = $target.DIRECTORY_SEPARATOR.'imsmanifest.xml';
        if (is_file($direct)) return $direct;

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target,\FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (strtolower($file->getFilename()) === 'imsmanifest.xml') return $file->getPathname();
        }
        return null;
    }

    private function storeFiles(Request $request, Lesson $lesson)
    {
        if (!$request->hasFile('files')) return;

        $allowed = [
            'pdf','html','htm','css','js','json','txt','xml','zip',
            'png','jpg','jpeg','gif','svg','webp','mp3','wav','ogg','m4a','mp4','webm',
            'doc','docx','xls','xlsx','ppt','pptx'
        ];

        foreach ($request->file('files') as $upload) {
            $ext = strtolower($upload->getClientOriginalExtension());
            if (!in_array($ext,$allowed,true)) abort(422,'Недопустимый тип файла: '.$upload->getClientOriginalName());

            $base = 'lessons/'.$lesson->id.'/files';
            $name = Str::uuid().($ext ? '.'.$ext : '');
            $path = $upload->storeAs($base,$name,'public');
            $launchPath = null;

            if ($ext === 'html' || $ext === 'htm') {
                $launchPath = $path;
            } elseif ($ext === 'zip') {
                $launchPath = $this->extractArchive($upload->getRealPath(),$lesson,(string)Str::uuid());
            }

            LessonFile::create([
                'lesson_id'=>$lesson->id,
                'original_name'=>$upload->getClientOriginalName(),
                'path'=>$path,
                'mime_type'=>$upload->getMimeType(),
                'size'=>$upload->getSize() ?: 0,
                'is_primary'=>!$lesson->files()->exists(),
                'launch_path'=>$launchPath
            ]);
        }
    }

    private function extractArchive($zipPath, Lesson $lesson, $folder)
    {
        if (!class_exists(ZipArchive::class)) return null;
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) return null;

        $allowed = ['html','htm','css','js','json','txt','xml','pdf','png','jpg','jpeg','gif','svg','webp','mp3','wav','ogg','m4a','mp4','webm','woff','woff2','ttf','eot'];
        for ($i=0; $i<$zip->numFiles; $i++) {
            $name = str_replace('\\','/',$zip->getNameIndex($i));
            if ($name === '' || Str::startsWith($name,'/') || Str::contains($name,'../')) {
                $zip->close();
                abort(422,'Архив содержит небезопасный путь');
            }
            if (Str::endsWith($name,'/')) continue;
            $ext = strtolower(pathinfo($name,PATHINFO_EXTENSION));
            if (!in_array($ext,$allowed,true)) {
                $zip->close();
                abort(422,'В архиве найден недопустимый файл: '.$name);
            }
        }

        $relative = 'lessons/'.$lesson->id.'/archive/'.$folder;
        $target = Storage::disk('public')->path($relative);
        if (!is_dir($target)) mkdir($target,0775,true);
        $zip->extractTo($target);
        $zip->close();

        foreach (['index.html','index.htm'] as $candidate) {
            if (file_exists($target.DIRECTORY_SEPARATOR.$candidate)) return $relative.'/'.$candidate;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target,\FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $name = strtolower($file->getFilename());
            if ($name === 'index.html' || $name === 'index.htm') {
                $full = str_replace('\\','/',$file->getPathname());
                $base = str_replace('\\','/',$target).'/';
                return $relative.'/'.ltrim(str_replace($base,'',$full),'/');
            }
        }
        return null;
    }
}
