@extends('layouts.app')

@section('title',$section->localizedTitle().' — Педслово')
@section('meta_description',$section->localizedDescription() ?: __('Учебные материалы раздела').' '.$section->localizedTitle())

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Главная') }}</a></li>
        @if($section->parent)
            <li class="breadcrumb-item"><a href="{{ route('section.show',$section->parent) }}">{{ $section->parent->localizedTitle() }}</a></li>
        @endif
        <li class="breadcrumb-item active">{{ $section->localizedTitle() }}</li></ol></nav>

    <div class="card card-soft mb-4"><div class="card-body p-4 p-lg-5"><div class="eyebrow">{{ __('Раздел образовательного ресурса') }}</div><h1 class="display-6 fw-bold text-wine">{{ $section->localizedTitle() }}</h1>
        @if($section->localizedDescription())
            <p class="lead text-muted mb-0">{{ $section->localizedDescription() }}</p>
        @endif
    </div></div>

    @if($section->children->count())
        <h2 class="h3 mb-3">{{ __('Направления') }}</h2><div class="row g-3 mb-5">
            @foreach($section->children as $child)
                <div class="col-md-6 col-lg-4"><a class="text-decoration-none text-dark" href="{{ route('section.show',$child) }}"><div class="card card-soft lift h-100"><div class="card-body p-4"><span class="badge bg-light text-dark">{{ $child->type }}</span><h3 class="h5 mt-2 mb-2">{{ $child->localizedTitle() }}</h3>@if($child->localizedDescription())<p class="small text-muted">{{ $child->localizedDescription() }}</p>@endif<span class="text-wine">{{ __('Перейти →') }}</span></div></div></a></div>
            @endforeach
        </div>
    @endif

    @if($section->courses->count())
        <h2 class="h3">{{ __('Учебные курсы') }}</h2><div class="row g-3 mb-5">
            @foreach($section->courses as $course)
                <div class="col-md-6"><div class="card card-soft h-100"><div class="card-body p-4"><span class="badge bg-light text-dark">{{ $course->study_year ? $course->study_year.' '.__('курс') : __('Программа') }}</span><h3 class="h5 mt-2">{{ __($course->title) }}</h3><p class="text-muted">{{ $course->description ? __($course->description) : '' }}</p><a class="btn btn-wine btn-sm" href="{{ route('courses.show',$course) }}">{{ __('Открыть курс') }}</a></div></div></div>
            @endforeach
        </div>
    @endif

    @if($section->materials->count())
        <h2 class="h3">{{ __('Материалы') }}</h2><div class="list-group shadow-sm rounded-4 overflow-hidden">
            @foreach($section->materials as $material)
                <a href="{{ route('material.show',$material) }}" class="list-group-item list-group-item-action p-3"><strong>{{ __($material->title) }}</strong><div class="small text-muted">{{ $material->annotation ? __($material->annotation) : '' }}</div></a>
            @endforeach
        </div>
    @endif
</div>
@endsection
