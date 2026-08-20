@extends('layouts.app')

@section('title',$lesson->title.' — Педслово')

@section('content')
<div class="container py-5">
    <a href="{{ route('courses.show',$lesson->course) }}" class="text-decoration-none">← {{ $lesson->course->title }}</a>

    <div class="row mt-3">
        <div class="col-lg-9">
            <h1>{{ $lesson->title }}</h1>
            @if($lesson->description)
                <p class="lead text-muted">{{ $lesson->description }}</p>
            @endif

            @if($lesson->material)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <div class="small text-muted mb-1">Материал портала</div>
                        <h4>{{ $lesson->material->title }}</h4>
                        @if($lesson->material->annotation)
                            <p>{{ $lesson->material->annotation }}</p>
                        @endif
                        <a class="btn btn-outline-primary" href="{{ route('material.show',$lesson->material) }}">Открыть материал</a>
                    </div>
                </div>
            @endif

            @if($lesson->scormPackages->count())
                <h2 class="h4 mt-4">Интерактивные задания и тесты</h2>
                <div class="row g-3 mb-4">
                    @foreach($lesson->scormPackages as $package)
                        <div class="col-md-6">
                            <div class="card border-success-subtle shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="small text-success mb-1">SCORM {{ $package->version }}</div>
                                    <h3 class="h5">{{ $package->title }}</h3>
                                    @if($package->pass_score !== null)
                                        <div class="small text-muted mb-2">Проходной балл: {{ $package->pass_score }}</div>
                                    @endif
                                    @if($package->max_attempts)
                                        <div class="small text-muted mb-3">Попыток: {{ $package->max_attempts }}</div>
                                    @endif
                                    <a class="btn btn-success" href="{{ route('scorm.launch',['scorm'=>$package,'lesson'=>$lesson->id]) }}">Запустить тест / модуль</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($lesson->files->count())
                <h2 class="h4 mt-4">Материалы урока</h2>
            @endif

            @foreach($lesson->files as $file)
                @php
                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                    $openUrl = $file->launch_url ?: $file->url;
                @endphp

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <strong>{{ $file->original_name }}</strong>
                                <div class="small text-muted">{{ number_format($file->size/1024,1,',',' ') }} КБ</div>
                            </div>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $openUrl }}" target="_blank">Открыть отдельно</a>
                        </div>

                        @if($ext === 'pdf')
                            <iframe src="{{ $file->url }}" style="width:100%;height:75vh;border:0"></iframe>
                        @elseif(in_array($ext,['html','htm','zip']) && $file->launch_url)
                            <iframe src="{{ $file->launch_url }}" sandbox="allow-scripts allow-forms allow-same-origin" style="width:100%;height:75vh;border:1px solid #ddd"></iframe>
                        @elseif(in_array($ext,['mp4','webm']))
                            <video controls preload="metadata" style="width:100%"><source src="{{ $file->url }}"></video>
                        @elseif(in_array($ext,['mp3','wav','ogg','m4a']))
                            <audio controls preload="metadata" style="width:100%"><source src="{{ $file->url }}"></audio>
                        @elseif(in_array($ext,['png','jpg','jpeg','gif','svg','webp']))
                            <img src="{{ $file->url }}" class="img-fluid rounded" alt="{{ $file->original_name }}">
                        @else
                            <div class="alert alert-light border mb-0">
                                Документ доступен для просмотра или скачивания:
                                <a href="{{ $file->url }}" target="_blank">{{ $file->original_name }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm position-sticky" style="top:1rem">
                <div class="card-body">
                    <h5>Статус урока</h5>
                    <p>{{ $lessonProgress->status }}</p>

                    @if($lessonProgress->score!==null)
                        <p>Балл: <strong>{{ $lessonProgress->score }}</strong></p>
                    @endif

                    @if(!$lesson->scormPackages->count() && !in_array($lessonProgress->status,['completed','passed']))
                        <form method="post" action="{{ route('learning.lesson.complete',$lesson) }}">
                            @csrf
                            <button class="btn btn-primary w-100">Отметить урок пройденным</button>
                        </form>
                    @elseif($lesson->scormPackages->count() && !in_array($lessonProgress->status,['completed','passed']))
                        <div class="small text-muted">Для завершения урока пройдите назначенный SCORM-тест или интерактивный модуль.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
