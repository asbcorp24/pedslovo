@extends('admin.layout')

@section('title', $lesson->exists ? 'Редактирование урока' : 'Новый урок')

@section('content')
<a href="{{ route('admin.courses.lessons.index', $course) }}" class="text-decoration-none">← Уроки курса</a>
<h1 class="mt-2">{{ $lesson->exists ? 'Редактирование урока' : 'Новый урок' }}</h1>

<div class="alert alert-light border">
    Один урок может одновременно содержать несколько документов, учебник/PDF, видео, аудио, HTML-материалы и один или несколько SCORM/iSpring-тестов.
    Все новые файлы урока хранятся в отдельной папке <code>storage/app/public/lessons/{id}/</code>.
</div>

<form method="post"
      enctype="multipart/form-data"
      action="{{ $lesson->exists ? route('admin.courses.lessons.update', [$course, $lesson]) : route('admin.courses.lessons.store', $course) }}">
    @csrf
    @if($lesson->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Название урока</label>
            <input class="form-control" name="title" required value="{{ old('title', $lesson->title) }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Представление урока</label>
            <select class="form-select" name="lesson_type">
                @foreach([
                    'mixed'=>'Составной урок',
                    'material'=>'Материал портала',
                    'scorm'=>'SCORM / тест',
                    'pdf'=>'PDF / учебник',
                    'html'=>'HTML',
                    'archive'=>'HTML-архив',
                    'file'=>'Документы',
                    'video'=>'Видео',
                    'audio'=>'Аудио',
                    'text'=>'Текстовый урок'
                ] as $key=>$label)
                    <option value="{{ $key }}" {{ old('lesson_type', $lesson->lesson_type ?: 'mixed')===$key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text">Это только визуальная категория. Она не ограничивает состав урока.</div>
        </div>

        <div class="col-12">
            <label class="form-label">Описание</label>
            <textarea class="form-control" rows="4" name="description">{{ old('description',$lesson->description) }}</textarea>
        </div>

        <div class="col-12">
            <div class="card admin-card border">
                <div class="card-body">
                    <h2 class="h5">Документы и медиа урока</h2>
                    <input class="form-control" type="file" name="files[]" multiple>
                    <div class="form-text">
                        Можно выбрать сразу несколько файлов: PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, HTML, ZIP с HTML-сайтом, MP4/WebM, MP3/WAV, изображения и т.д. Максимум 100 МБ на файл.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card admin-card border border-success-subtle">
                <div class="card-body">
                    <h2 class="h5">SCORM / iSpring внутри этого урока</h2>
                    <label class="form-label">Загрузить новые SCORM ZIP</label>
                    <input class="form-control" type="file" name="scorm_files[]" accept=".zip" multiple>
                    <div class="form-text mb-3">
                        SCORM загружается прямо в папку урока: <code>lessons/{id}/scorm/...</code>. Архив должен содержать <code>imsmanifest.xml</code>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Макс. попыток</label>
                            <input class="form-control" type="number" min="1" max="100" name="scorm_max_attempts" value="{{ old('scorm_max_attempts') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Проходной балл</label>
                            <input class="form-control" type="number" min="0" max="100" step="0.01" name="scorm_pass_score" value="{{ old('scorm_pass_score') }}">
                        </div>
                    </div>

                    @if($scormPackages->count())
                        <hr>
                        <div class="fw-semibold mb-2">Привязать уже загруженные SCORM-пакеты</div>
                        @php
                            $selectedScorm = old('scorm_package_ids', $lesson->exists ? $lesson->scormPackages->pluck('id')->all() : []);
                        @endphp
                        <div class="row g-2" style="max-height:240px;overflow:auto">
                            @foreach($scormPackages as $package)
                                <div class="col-md-6">
                                    <label class="border rounded p-2 d-block">
                                        <input type="checkbox" name="scorm_package_ids[]" value="{{ $package->id }}" {{ in_array($package->id,$selectedScorm) ? 'checked' : '' }}>
                                        {{ $package->title }}
                                        <small class="d-block text-muted">SCORM {{ $package->version }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Материал портала (необязательно)</label>
            <select class="form-select" name="material_id">
                <option value="">—</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" {{ (string)old('material_id',$lesson->material_id)===(string)$material->id ? 'selected' : '' }}>{{ $material->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Порядок</label>
            <input type="number" min="0" class="form-control" name="sort_order" value="{{ old('sort_order',$lesson->sort_order ?? 0) }}">
        </div>

        <div class="col-md-2 form-check mt-5 ms-2">
            <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ old('is_required',$lesson->exists ? $lesson->is_required : true) ? 'checked' : '' }}>
            <label class="form-check-label">Обязательный</label>
        </div>

        <div class="col-md-2 form-check mt-5">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$lesson->exists ? $lesson->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label">Опубликован</label>
        </div>
    </div>

    <button class="btn btn-primary mt-4">Сохранить урок</button>
</form>

@if($lesson->exists)
    @if($lesson->scormPackages->count())
        <div class="card admin-card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="h5">SCORM-модули урока</h2>
                <div class="list-group list-group-flush">
                    @foreach($lesson->scormPackages as $package)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div><strong>{{ $package->title }}</strong><div class="small text-muted">SCORM {{ $package->version }} · проходной балл {{ $package->pass_score ?: 'не задан' }}</div></div>
                            <a class="btn btn-sm btn-outline-success" target="_blank" href="{{ route('scorm.launch',['scorm'=>$package,'lesson'=>$lesson->id]) }}">Запустить</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($lesson->files->count())
        <div class="card admin-card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="h5">Документы и медиа урока</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Файл</th><th>Тип</th><th>Размер</th><th>Просмотр</th><th></th></tr></thead>
                        <tbody>
                        @foreach($lesson->files as $file)
                            <tr>
                                <td>{{ $file->original_name }} @if($file->is_primary)<span class="badge text-bg-primary">основной</span>@endif</td>
                                <td>{{ $file->mime_type ?: '—' }}</td>
                                <td>{{ number_format($file->size/1024,1,',',' ') }} КБ</td>
                                <td><a target="_blank" href="{{ $file->launch_url ?: $file->url }}">Открыть</a></td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('admin.courses.lessons.files.destroy',[$course,$lesson,$file]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить файл?')">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
