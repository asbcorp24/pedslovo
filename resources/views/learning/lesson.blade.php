@extends('layouts.app')

@section('title',$lesson->title.' — Педслово')

@section('content')
<div class="container py-5">
    <a href="{{ route('courses.show',$lesson->course) }}" class="text-decoration-none">← {{ $lesson->course->title }}</a>

    <div class="row mt-3">
        <div class="col-lg-9">
            <h1>{{ $lesson->title }}</h1>
            @if($lesson->description)<p class="lead text-muted">{{ $lesson->description }}</p>@endif

            @if($lesson->material)
                @php($materialState=$resourceProgress->get('material:'.$lesson->material_id))
                <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                    <div class="d-flex justify-content-between gap-2 align-items-start">
                        <div><div class="small text-muted mb-1">{{ __('Материал портала') }}</div><h4>{{ $lesson->material->title }}</h4></div>
                        <span class="badge {{ $lesson->material_required ? 'text-bg-warning' : 'text-bg-light' }}">{{ $lesson->material_required ? __('ui.required') : __('ui.optional') }}</span>
                    </div>
                    @if($lesson->material->annotation)<p>{{ $lesson->material->annotation }}</p>@endif
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-primary" href="{{ route('material.show',$lesson->material) }}">{{ __('Открыть материал') }}</a>
                        @if($materialState && $materialState['completed'])
                            <span class="btn btn-success disabled">✓ {{ __('ui.done') }}</span>
                        @else
                            <form method="post" action="{{ route('learning.resource.complete',[$lesson,'material',$lesson->material_id]) }}">@csrf<button class="btn btn-outline-success">{{ __('ui.mark_done') }}</button></form>
                        @endif
                    </div>
                </div></div>
            @endif

            @if($lesson->links->count())
                <h2 class="h4 mt-4">{{ __('Видео урока') }}</h2>
                @foreach($lesson->links as $link)
                    @php($linkState=$resourceProgress->get('link:'.$link->id))
                    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                            <div><strong>{{ $link->title ?: __('Видео') }}</strong> <span class="badge text-bg-light">{{ $link->provider }}</span> <span class="badge {{ $link->is_required ? 'text-bg-warning' : 'text-bg-light' }}">{{ $link->is_required ? __('ui.required') : __('ui.optional') }}</span></div>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $link->url }}" target="_blank" rel="noopener">{{ __('Открыть отдельно') }}</a>
                        </div>
                        @if($link->embed_url)
                            <div class="ratio ratio-16x9"><iframe src="{{ $link->embed_url }}" title="{{ $link->title ?: __('Видео урока') }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe></div>
                        @else
                            <div class="alert alert-light border">{{ __('Видео доступно по внешней ссылке:') }} <a href="{{ $link->url }}" target="_blank" rel="noopener">{{ $link->url }}</a></div>
                        @endif
                        <div class="mt-3">
                            @if($linkState && $linkState['completed'])<span class="btn btn-success disabled">✓ {{ __('ui.done') }}</span>@else<form method="post" action="{{ route('learning.resource.complete',[$lesson,'link',$link->id]) }}">@csrf<button class="btn btn-outline-success">{{ __('ui.mark_done') }}</button></form>@endif
                        </div>
                    </div></div>
                @endforeach
            @endif

            @if($lesson->scormPackages->count())
                <h2 class="h4 mt-4">{{ __('Интерактивные задания и тесты') }}</h2>
                <div class="row g-3 mb-4">
                    @foreach($lesson->scormPackages as $package)
                        @php($scormState=$resourceProgress->get('scorm:'.$package->id))
                        <div class="col-md-6"><div class="card border-success-subtle shadow-sm h-100"><div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-2"><div class="small text-success mb-1">SCORM {{ $package->version }}</div><span class="badge {{ $package->pivot->is_required ? 'text-bg-warning' : 'text-bg-light' }}">{{ $package->pivot->is_required ? __('ui.required') : __('ui.optional') }}</span></div>
                            <h3 class="h5">{{ $package->title }}</h3>
                            @if($package->pass_score !== null)<div class="small text-muted mb-2">{{ __('Проходной балл:') }} {{ $package->pass_score }}</div>@endif
                            @if($package->max_attempts)<div class="small text-muted mb-3">{{ __('Попыток:') }} {{ $package->max_attempts }}</div>@endif
                            @if($scormState && $scormState['completed'])<div class="alert alert-success py-2">✓ {{ __('ui.done') }}</div>@endif
                            <a class="btn btn-success" href="{{ route('scorm.launch',['scorm'=>$package,'lesson'=>$lesson->id]) }}">{{ __('Запустить тест / модуль') }}</a>
                        </div></div></div>
                    @endforeach
                </div>
            @endif

            @if($lesson->files->count())<h2 class="h4 mt-4">{{ __('Материалы урока') }}</h2>@endif
            @foreach($lesson->files as $file)
                @php
                    $ext=strtolower(pathinfo($file->original_name,PATHINFO_EXTENSION));
                    $openUrl=$file->launch_url ?: $file->url;
                    $fileState=$resourceProgress->get('file:'.$file->id);
                @endphp
                <div class="card border-0 shadow-sm mb-3"><div class="card-body p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div><strong>{{ $file->original_name }}</strong> <span class="badge {{ $file->is_required ? 'text-bg-warning' : 'text-bg-light' }}">{{ $file->is_required ? __('ui.required') : __('ui.optional') }}</span><div class="small text-muted">{{ number_format($file->size/1024,1,',',' ') }} КБ</div></div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $openUrl }}" target="_blank">{{ __('Открыть отдельно') }}</a>
                    </div>
                    @if($ext==='pdf')<iframe src="{{ $file->url }}" style="width:100%;height:75vh;border:0"></iframe>
                    @elseif(in_array($ext,['html','htm','zip']) && $file->launch_url)<iframe src="{{ $file->launch_url }}" sandbox="allow-scripts allow-forms allow-same-origin" style="width:100%;height:75vh;border:1px solid #ddd"></iframe>
                    @elseif(in_array($ext,['mp4','webm']))<video controls preload="metadata" style="width:100%"><source src="{{ $file->url }}"></video>
                    @elseif(in_array($ext,['mp3','wav','ogg','m4a']))<audio controls preload="metadata" style="width:100%"><source src="{{ $file->url }}"></audio>
                    @elseif(in_array($ext,['png','jpg','jpeg','gif','svg','webp']))<img src="{{ $file->url }}" class="img-fluid rounded" alt="{{ $file->original_name }}">
                    @else<div class="alert alert-light border">{{ __('Документ доступен для просмотра или скачивания:') }} <a href="{{ $file->url }}" target="_blank">{{ $file->original_name }}</a></div>@endif
                    <div class="mt-3">@if($fileState && $fileState['completed'])<span class="btn btn-success disabled">✓ {{ __('ui.done') }}</span>@else<form method="post" action="{{ route('learning.resource.complete',[$lesson,'file',$file->id]) }}">@csrf<button class="btn btn-outline-success">{{ __('ui.mark_done') }}</button></form>@endif</div>
                </div></div>
            @endforeach
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm position-sticky" style="top:1rem"><div class="card-body">
                <h5>{{ __('Статус урока') }}</h5>
                <p>{{ $lessonProgress->status }}</p>
                @if($lessonProgress->score!==null)<p>{{ __('Балл:') }} <strong>{{ $lessonProgress->score }}</strong></p>@endif
                @if($resourceState['required_count'] > 0)
                    <div class="mb-3"><div class="small text-muted">{{ __('ui.required_progress') }}</div><strong>{{ $resourceState['required_done'] }} / {{ $resourceState['required_count'] }}</strong></div>
                    <div class="progress mb-3"><div class="progress-bar" style="width:{{ $resourceState['required_count'] ? round($resourceState['required_done']/$resourceState['required_count']*100) : 0 }}%"></div></div>
                    @if($resourceState['all_required_done'])<div class="alert alert-success py-2">✓ {{ __('ui.lesson_completed') }}</div>@else<div class="small text-muted">{{ __('ui.finish_required_first') }}</div>@endif
                @elseif(!in_array($lessonProgress->status,['completed','passed']))
                    <form method="post" action="{{ route('learning.lesson.complete',$lesson) }}">@csrf<button class="btn btn-primary w-100">{{ __('Отметить урок пройденным') }}</button></form>
                @endif
            </div></div>
        </div>
    </div>
</div>
@endsection
