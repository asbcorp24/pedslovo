@extends('layouts.app')

@section('title', __('Педслово — учебная часть ЧМУ им. Ф.П. Павлова'))

@section('content')
@php
    $static = function (string $key, $fallback = '') {
        return \Illuminate\Support\Facades\Lang::has($key) ? __($key) : $fallback;
    };
@endphp
<div class="container py-5">
    <section class="hero p-4 p-md-5 mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8 position-relative" style="z-index:1">
                <div class="text-uppercase small fw-bold gold mb-2">
                    {{ $static('content.home.badge', $home['home_badge'] ?? __('Учебная часть')) }}
                </div>
                <h1 class="display-4 fw-bold">
                    {{ $static('content.home.title', $home['home_title'] ?? __('Педслово — цифровая образовательная среда училища')) }}
                </h1>
                <p class="lead text-white-50">
                    {{ $static('content.home.subtitle', $home['home_subtitle'] ?? __('Учебные материалы, курсы и цифровые сервисы для студентов и преподавателей.')) }}
                </p>

                @auth
                    <a href="{{ route('cabinet') }}" class="btn btn-light btn-lg">{{ __('Перейти в личный кабинет') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg">{{ __('Войти в систему') }}</a>
                @endauth
            </div>
        </div>
    </section>

    <div class="row g-4 mb-5">
        @foreach($sections as $section)
            @php
                $sectionKey = 'content.sections.'.$section->slug;
                $sectionTitle = $static($sectionKey.'.title', $section->title);
                $sectionDescription = $static($sectionKey.'.description', $section->description ?: __('Программы, материалы и учебные направления'));
            @endphp
            <div class="col-md-6 col-xl-3">
                <a class="text-decoration-none text-dark" href="{{ route('section.show', $section) }}">
                    <div class="card section-card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="gold fs-2 mb-2">♪</div>
                            <h2 class="h4">{{ $sectionTitle }}</h2>
                            <p class="text-muted">{{ $sectionDescription }}</p>
                            <span class="small fw-semibold">{{ __('Открыть раздел →') }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if($specialties->count())
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <div class="text-uppercase small fw-bold gold">{{ __('Среднее профессиональное образование') }}</div>
                <h2 class="h2 mb-0">{{ __('Специальности училища') }}</h2>
            </div>
        </div>

        <div class="row g-3 mb-5">
            @foreach($specialties as $specialty)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('section.show', $specialty) }}" class="text-decoration-none text-dark">
                        <div class="card content-card shadow-sm h-100">
                            <div class="card-body p-4">
                                <h3 class="h5">{{ __($specialty->title) }}</h3>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @for($year = 1; $year <= 4; $year++)
                                        <span class="year-pill">{{ $year }} {{ __('курс') }}</span>
                                    @endfor
                                </div>
                                <div class="small text-muted mt-3">
                                    {{ $specialty->courses_count }} {{ __('учебных дисциплин') }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <section class="bg-white rounded-4 shadow-sm p-4 p-md-5 mb-5">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div class="text-uppercase small fw-bold gold">{{ __('О системе') }}</div>
                <h2>{{ __($home['home_about_title'] ?? 'Учиться и преподавать в одной системе') }}</h2>
            </div>
            <div class="col-lg-7">
                <p class="lead text-muted mb-0">
                    {{ __($home['home_about_text'] ?? 'Педслово объединяет учебные материалы, курсы, результаты и цифровые сервисы учебной части.') }}
                </p>
            </div>
        </div>
    </section>

    @if($featuredCourses->count())
        <h2 class="h3 mb-3">{{ __('Учебные курсы') }}</h2>
        <div class="row g-3 mb-5">
            @foreach($featuredCourses as $course)
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('courses.show', $course) }}" class="text-decoration-none text-dark">
                        <div class="card content-card shadow-sm h-100">
                            <div class="card-body">
                                <span class="badge text-bg-light">
                                    {{ $course->study_year ? $course->study_year . ' ' . __('курс') : __('курс') }}
                                </span>
                                <h3 class="h6 mt-2">{{ __($course->title) }}</h3>
                                <div class="small text-muted">{{ optional($course->section)->title ? __(optional($course->section)->title) : '' }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($latest->count())
        <h2 class="h3 mb-3">{{ __('Новые материалы') }}</h2>
        <div class="row g-3">
            @foreach($latest as $material)
                <div class="col-md-6">
                    <div class="card content-card shadow-sm">
                        <div class="card-body">
                            <span class="badge text-bg-light">{{ __($material->material_type) }}</span>
                            <h3 class="h5 mt-2">
                                <a href="{{ route('material.show', $material) }}" class="text-dark text-decoration-none">
                                    {{ __($material->title) }}
                                </a>
                            </h3>
                            <p class="text-muted mb-0">{{ $material->annotation ? __($material->annotation) : '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
