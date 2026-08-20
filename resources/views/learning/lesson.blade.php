@extends('layouts.app')

@section('title',$lesson->title.' — Педслово')

@section('content')
<div class="container py-5">
    <a href="{{ route('courses.show',$lesson->course) }}" class="text-decoration-none">← {{ $lesson->course->title }}</a>
    <div class="row mt-3">
        <div class="col-lg-8">
            <h1>{{ $lesson->title }}</h1><p class="lead text-muted">{{ $lesson->description }}</p>
            @if($lesson->material)
                <div class="card border-0 shadow-sm"><div class="card-body p-4"><h4>{{ $lesson->material->title }}</h4>@if($lesson->material->annotation)<p>{{ $lesson->material->annotation }}</p>@endif<a class="btn btn-outline-primary" href="{{ route('material.show',$lesson->material) }}">Открыть материал</a></div></div>
            @endif
            @if($lesson->scormPackage)
                <div class="card border-0 shadow-sm mt-3"><div class="card-body p-4"><h4>Интерактивный модуль iSpring</h4><p class="text-muted">Результат прохождения и балл сохраняются автоматически.</p><a class="btn btn-success" href="{{ route('scorm.launch',['scorm'=>$lesson->scormPackage,'lesson'=>$lesson->id]) }}">Запустить SCORM</a></div></div>
            @endif
        </div>
        <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body"><h5>Статус</h5><p>{{ $lessonProgress->status }}</p>
            @if($lessonProgress->score !== null)
                <p>Балл: <strong>{{ $lessonProgress->score }}</strong></p>
            @endif
            @if($lesson->lesson_type !== 'scorm' && !in_array($lessonProgress->status,['completed','passed']))
                <form method="post" action="{{ route('learning.lesson.complete',$lesson) }}">@csrf<button class="btn btn-primary w-100">Отметить пройденным</button></form>
            @endif
        </div></div></div>
    </div>
</div>
@endsection
