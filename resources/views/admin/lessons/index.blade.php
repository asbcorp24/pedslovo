@extends('admin.layout')

@section('title','Уроки — '.$course->title)

@section('content')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">← Курсы</a>
        <h1 class="mt-2">Уроки: {{ $course->title }}</h1>
    </div>
    <a href="{{ route('admin.courses.lessons.create',$course) }}" class="btn btn-primary">Добавить урок</a>
</div>

<div class="alert alert-light border mt-3">Перетаскивайте строки ↕ — порядок уроков сохранится автоматически. Файлы каждого урока лежат в собственной папке.</div>

<div class="card admin-card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>#</th><th>Название</th><th>Тип</th><th>Источник</th><th>Файлов</th><th></th></tr></thead>
            <tbody class="js-sortable" data-sort-url="{{ route('admin.sort.update','lessons') }}">
            @forelse($course->lessons as $lesson)
                <tr data-sort-id="{{ $lesson->id }}">
                    <td class="drag-handle">↕</td>
                    <td>{{ $lesson->sort_order }}</td>
                    <td><strong>{{ $lesson->title }}</strong></td>
                    <td>{{ $lesson->lesson_type }}</td>
                    <td>{{ optional($lesson->material)->title ?: (optional($lesson->scormPackage)->title ?: ($lesson->files->first() ? $lesson->files->first()->original_name : '—')) }}</td>
                    <td>{{ $lesson->files->count() }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.courses.lessons.edit',[$course,$lesson]) }}">Изменить</a>
                        <form class="d-inline" method="post" action="{{ route('admin.courses.lessons.destroy',[$course,$lesson]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить урок вместе с его файлами?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted p-4">Уроков пока нет.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
