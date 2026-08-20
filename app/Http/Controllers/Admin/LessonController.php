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
        $course->load(['lessons.material','lessons.scormPackage','lessons.files']);
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
        $this->storeFiles($request, $lesson);
        return redirect()->route('admin.courses.lessons.index',$course)->with('success','Урок добавлен');
    }

    public function edit(Course $course, Lesson $lesson)
    {
        abort_unless((int)$lesson->course_id === (int)$course->id,404);
        $lesson->load('files');
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
        $this->storeFiles($request, $lesson);
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
            'lesson_type'=>'required|in:material,scorm,pdf,html,archive,file,video,audio,text',
            'material_id'=>'nullable|exists:materials,id',
            'scorm_package_id'=>'nullable|exists:scorm_packages,id',
            'sort_order'=>'nullable|integer|min:0',
            'is_required'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
            'files'=>'nullable|array',
            'files.*'=>'file|max:102400'
        ]);
        unset($data['files']);
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active');
        if ($data['lesson_type'] === 'scorm') $data['material_id'] = null;
        if ($data['lesson_type'] !== 'scorm') $data['scorm_package_id'] = null;
        if ($data['lesson_type'] !== 'material') $data['material_id'] = null;
        return $data;
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
            if (!in_array($ext, $allowed, true)) {
                abort(422, 'Недопустимый тип файла: '.$upload->getClientOriginalName());
            }

            $base = 'lessons/'.$lesson->id;
            $name = Str::uuid().($ext ? '.'.$ext : '');
            $path = $upload->storeAs($base, $name, 'public');
            $launchPath = null;

            if ($ext === 'html' || $ext === 'htm') {
                $launchPath = $path;
            } elseif ($ext === 'zip') {
                $launchPath = $this->extractArchive($upload->getRealPath(), $lesson, (string) Str::uuid());
            }

            $file = LessonFile::create([
                'lesson_id'=>$lesson->id,
                'original_name'=>$upload->getClientOriginalName(),
                'path'=>$path,
                'mime_type'=>$upload->getMimeType(),
                'size'=>$upload->getSize() ?: 0,
                'is_primary'=>!$lesson->files()->exists(),
                'launch_path'=>$launchPath
            ]);

            if (!$lesson->files()->where('is_primary',true)->exists()) {
                $file->update(['is_primary'=>true]);
            }
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

        $candidates = ['index.html','index.htm'];
        foreach ($candidates as $candidate) {
            if (file_exists($target.DIRECTORY_SEPARATOR.$candidate)) return $relative.'/'.$candidate;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS));
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
