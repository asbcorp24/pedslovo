@extends('admin.layout')

@section('title', $lesson->exists ? 'Редактирование урока' : 'Новый урок')

@section('content')
<a href="{{ route('admin.courses.lessons.index', $course) }}" class="text-decoration-none">← Уроки курса</a>
<h1 class="mt-2">{{ $lesson->exists ? 'Редактирование урока' : 'Новый урок' }}</h1>

<form method="post"
      enctype="multipart/form-data"
      action="{{ $lesson->exists ? route('admin.courses.lessons.update', [$course, $lesson]) : route('admin.courses.lessons.store', $course) }}">
    @csrf
    @if($lesson->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Название</label>
            <input class="form-control" name="title" required value="{{ old('title', $lesson->title) }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Тип урока</label>
            <select class="form-select" name="lesson_type" id="lessonType">
                @foreach([
                    'material'=>'Материал портала',
                    'scorm'=>'iSpring / SCORM',
                    'pdf'=>'PDF',
                    'html'=>'HTML',
                    'archive'=>'Архив сайта / HTML ZIP',
                    'file'=>'Файл для скачивания',
                    'video'=>'Видео',
                    'audio'=>'Аудио',
                    'text'=>'Текстовый урок'
                ] as $key=>$label)
                    <option value="{{ $key }}" {{ old('lesson_type', $lesson->lesson_type)===$key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Материал портала</label>
            <select class="form-select" name="material_id">
                <option value="">—</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" {{ (string)old('material_id',$lesson->material_id)===(string)$material->id ? 'selected' : '' }}>{{ $material->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">SCORM-пакет</label>
            <select class="form-select" name="scorm_package_id">
                <option value="">—</option>
                @foreach($scormPackages as $package)
                    <option value="{{ $package->id }}" {{ (string)old('scorm_package_id',$lesson->scorm_package_id)===(string)$package->id ? 'selected' : '' }}>{{ $package->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Файлы урока</label>
            <input class="form-control" type="file" name="files[]" multiple>
            <div class="form-text">
                Каждый урок хранит файлы отдельно: <code>storage/app/public/lessons/{id}/</code>.
                Можно загружать PDF, HTML, ZIP с HTML-сайтом, видео, аудио, изображения и офисные документы.
                Максимум 100 МБ на файл.
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Описание</label>
            <textarea class="form-control" rows="4" name="description">{{ old('description',$lesson->description) }}</textarea>
        </div>

        <div class="col-md-3">
            <label class="form-label">Порядок</label>
            <input type="number" min="0" class="form-control" name="sort_order" value="{{ old('sort_order',$lesson->sort_order ?? 0) }}">
        </div>

        <div class="col-md-3 form-check mt-5 ms-2">
            <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ old('is_required',$lesson->exists ? $lesson->is_required : true) ? 'checked' : '' }}>
            <label class="form-check-label">Обязательный</label>
        </div>

        <div class="col-md-3 form-check mt-5">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$lesson->exists ? $lesson->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label">Опубликован</label>
        </div>
    </div>

    <button class="btn btn-primary mt-4">Сохранить</button>
</form>

@if($lesson->exists && $lesson->files->count())
    <div class="card admin-card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">Загруженные файлы</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Файл</th><th>Тип</th><th>Размер</th><th>Запуск</th><th></th></tr></thead>
                    <tbody>
                    @foreach($lesson->files as $file)
                        <tr>
                            <td>{{ $file->original_name }} @if($file->is_primary)<span class="badge text-bg-primary">основной</span>@endif</td>
                            <td>{{ $file->mime_type ?: '—' }}</td>
                            <td>{{ number_format($file->size/1024,1,',',' ') }} КБ</td>
                            <td>
                                @if($file->launch_url)
                                    <a target="_blank" href="{{ $file->launch_url }}">Открыть</a>
                                @else
                                    <a target="_blank" href="{{ $file->url }}">Файл</a>
                                @endif
                            </td>
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
@endsection
