@extends('layouts.app')

@section('title',$section->title.' — 1–4 курс | Педслово')
@section('meta_description','Учебные материалы и дисциплины специальности '.$section->title.' по 1, 2, 3 и 4 курсам.')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
        @if($section->parent)
            <li class="breadcrumb-item"><a href="{{ route('section.show',$section->parent) }}">{{ $section->parent->title }}</a></li>
        @endif
        <li class="breadcrumb-item active">{{ $section->title }}</li></ol></nav>
    <div class="card card-soft mb-5"><div class="card-body p-4 p-lg-5"><div class="eyebrow">Специальность</div><h1 class="display-6 fw-bold text-wine">{{ $section->title }}</h1><p class="lead text-muted mb-0">{{ $section->description ?: 'Материалы учебной части распределены по годам обучения. Выберите курс, чтобы перейти к дисциплинам и учебным модулям.' }}</p></div></div>
    <ul class="nav nav-pills gap-2 mb-4" role="tablist">
        @for($y=1;$y<=4;$y++)
            <li class="nav-item"><button class="nav-link {{ $y===1 ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#year{{ $y }}">{{ $y }} курс</button></li>
        @endfor
    </ul>
    <div class="tab-content">
        @for($y=1;$y<=4;$y++)
            <div class="tab-pane fade {{ $y===1 ? 'show active' : '' }}" id="year{{ $y }}"><div class="row g-3">
                @php($yearCourses=$section->courses->where('study_year',$y))
                @forelse($yearCourses as $course)
                    <div class="col-md-6 col-lg-4"><div class="card card-soft lift h-100"><div class="card-body p-4"><div class="small text-muted mb-2">{{ $y }} курс</div><h3 class="h5">{{ $course->title }}</h3><p class="text-muted small">{{ \Illuminate\Support\Str::limit($course->description,130) }}</p>
                        @if($course->instructor)
                            <div class="small mb-3">Преподаватель: {{ $course->instructor->name }}</div>
                        @endif
                        <a class="btn btn-wine btn-sm" href="{{ route('courses.show',$course) }}">Перейти к курсу</a>
                    </div></div></div>
                @empty
                    <div class="col-12"><div class="alert alert-light border">Для {{ $y }} курса материалы пока не опубликованы.</div></div>
                @endforelse
            </div></div>
        @endfor
    </div>
    @if($section->materials->count())
        <div class="mt-5"><h2 class="h3">Общие материалы специальности</h2><div class="list-group">
            @foreach($section->materials as $m)
                <a class="list-group-item list-group-item-action" href="{{ route('material.show',$m) }}">{{ $m->title }}</a>
            @endforeach
        </div></div>
    @endif
</div>
@endsection
