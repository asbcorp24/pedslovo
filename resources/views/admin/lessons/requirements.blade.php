@extends('admin.layout')

@section('title','Обязательные элементы — '.$lesson->title)

@section('content')
<a href="{{ route('admin.courses.lessons.index',$course) }}" class="text-decoration-none">← Уроки курса</a>
<h1 class="mt-2">Обязательные элементы: {{ $lesson->title }}</h1>
<p class="text-muted">Урок завершится автоматически только после выполнения всех отмеченных обязательных элементов.</p>

<form method="post" action="{{ route('admin.courses.lessons.requirements.update',[$course,$lesson]) }}" class="card admin-card shadow-sm">
    @csrf
    @method('PUT')
    <div class="card-body p-4">
        @if($lesson->material)
            <label class="d-flex gap-3 align-items-start border rounded p-3 mb-3">
                <input class="form-check-input mt-1" type="checkbox" name="material_required" value="1" {{ $lesson->material_required ? 'checked' : '' }}>
                <span><strong>Материал портала: {{ $lesson->material->title }}</strong><small class="d-block text-muted">Студент отмечает материал изученным.</small></span>
            </label>
        @endif

        @foreach($lesson->files as $file)
            <label class="d-flex gap-3 align-items-start border rounded p-3 mb-2">
                <input class="form-check-input mt-1" type="checkbox" name="required_file_ids[]" value="{{ $file->id }}" {{ $file->is_required ? 'checked' : '' }}>
                <span><strong>Файл: {{ $file->original_name }}</strong><small class="d-block text-muted">PDF, видео, аудио, документ или HTML.</small></span>
            </label>
        @endforeach

        @foreach($lesson->links as $link)
            <label class="d-flex gap-3 align-items-start border rounded p-3 mb-2">
                <input class="form-check-input mt-1" type="checkbox" name="required_link_ids[]" value="{{ $link->id }}" {{ $link->is_required ? 'checked' : '' }}>
                <span><strong>{{ ucfirst($link->provider) }}: {{ $link->title ?: $link->url }}</strong><small class="d-block text-muted text-break">{{ $link->url }}</small></span>
            </label>
        @endforeach

        @foreach($lesson->scormPackages as $package)
            <label class="d-flex gap-3 align-items-start border border-success-subtle rounded p-3 mb-2">
                <input class="form-check-input mt-1" type="checkbox" name="required_scorm_ids[]" value="{{ $package->id }}" {{ $package->pivot->is_required ? 'checked' : '' }}>
                <span><strong>SCORM {{ $package->version }}: {{ $package->title }}</strong><small class="d-block text-muted">Выполнение определяется автоматически по результату SCORM.</small></span>
            </label>
        @endforeach

        @if(!$lesson->material && !$lesson->files->count() && !$lesson->links->count() && !$lesson->scormPackages->count())
            <div class="alert alert-warning mb-0">В уроке пока нет элементов.</div>
        @endif
    </div>
    <div class="card-footer bg-white border-0 p-4 pt-0"><button class="btn btn-primary">Сохранить обязательность</button></div>
</form>
@endsection
