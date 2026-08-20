@extends('layouts.app')

@section('title',$lesson->title.' — Педслово')

@section('content')
<div class="container py-5">
    <a href="{{ route('courses.show',$lesson->course) }}" class="text-decoration-none">← {{ $lesson->course->title }}</a>
    <div class="row mt-3">
        <div class="col-lg-9">
            <h1>{{ $lesson->title }}</h1>
            <p class="lead text-muted">{{ $lesson->description }}</p>

            @if($lesson->material)
                <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                    <h4>{{ $lesson->material->title }}</h4>
                    @if($lesson->material->annotation)<p>{{ $lesson->material->annotation }}</p>@endif
                    <a class="btn btn-outline-primary" href="{{ route('material.show',$lesson->material) }}">Открыть материал</a>
                </div></div>
            @endif

            @if($lesson->scormPackage)
                <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                    <h4>Интерактивный модуль iSpring</h4>
                    <p class="text-muted">Результат прохождения и балл сохраняются автоматически.</p>
                    <a class="btn btn-success" href="{{ route('scorm.launch',['scorm'=>$lesson->scormPackage,'lesson'=>$lesson->id]) }}">Запустить SCORM</a>
                </div></div>
            @endif

            @foreach($lesson->files as $file)
                @php
                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                    $openUrl = $file->launch_url ?: $file->url;
                @endphp

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>{{ $file->original_name }}</strong>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $file->url }}" target="_blank">Открыть отдельно</a>
                        </div>

                        @if($ext === 'pdf')
                            <iframe src="{{ $file->url }}" style="width:100%;height:75vh;border:0"></iframe>
                        @elseif(in_array($ext,['html','htm','zip']) && $file->launch_url)
                            <iframe src="{{ $file->launch_url }}" sandbox="allow-scripts allow-forms allow-same-origin" style="width:100%;height:75vh;border:1px solid #ddd"></iframe>
                        @elseif(in_array($ext,['mp4','webm']))
                            <video controls style="width:100%"><source src="{{ $file->url }}"></video>
                        @elseif(in_array($ext,['mp3','wav','ogg','m4a']))
                            <audio controls style="width:100%"><source src="{{ $file->url }}"></audio>
                        @elseif(in_array($ext,['png','jpg','jpeg','gif','svg','webp']))
                            <img src="{{ $file->url }}" class="img-fluid rounded" alt="{{ $file->original_name }}">
                        @else
                            <div class="alert alert-light border mb-0">Файл доступен для просмотра или скачивания: <a href="{{ $openUrl }}" target="_blank">{{ $file->original_name }}</a></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <h5>Статус</h5>
                <p>{{ $lessonProgress->status }}</p>
                @if($lessonProgress->score!==null)<p>Балл: <strong>{{ $lessonProgress->score }}</strong></p>@endif
                @if($lesson->lesson_type!=='scorm' && !in_array($lessonProgress->status,['completed','passed']))
                    <form method="post" action="{{ route('learning.lesson.complete',$lesson) }}">
                        @csrf
                        <button class="btn btn-primary w-100">Отметить пройденным</button>
                    </form>
                @endif
            </div></div>
        </div>
    </div>
</div>
@endsection
