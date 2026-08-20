@extends('admin.layout')

@section('title',$section->exists ? 'Изменить раздел' : 'Новый раздел')

@section('content')
@php
    $languageLabels = ['ru'=>'Русский','cv'=>'Чувашский','mhr'=>'Марийский','tt'=>'Татарский'];
    $translations = $section->exists && $section->relationLoaded('translations') ? $section->translations->keyBy('locale') : collect();
@endphp

<h1 class="mb-4">{{ $section->exists ? 'Изменить раздел' : 'Новый раздел' }}</h1>
<form method="post" action="{{ $section->exists ? route('admin.sections.update',$section) : route('admin.sections.store') }}">
    @csrf
    @if($section->exists) @method('PUT') @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Служебные параметры</h2>
            <div class="mb-3">
                <label class="form-label">Родительский раздел</label>
                <select class="form-select" name="parent_id">
                    <option value="">Нет</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" @selected(old('parent_id',$section->parent_id)==$p->id)>{{ $p->localizedTitle('ru') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug',$section->slug) }}" placeholder="создастся из русского названия"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Тип</label><input class="form-control" name="type" value="{{ old('type',$section->type) }}" placeholder="audience, specialty, program"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Порядок</label><input class="form-control" type="number" name="sort_order" value="{{ old('sort_order',$section->sort_order ?? 0) }}"></div>
            </div>
            <div class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$section->exists ? $section->is_active : true))><label class="form-check-label">Активен</label></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="h5 mb-0">Название и описание на языках</h2><span class="text-muted small">Русский обязателен, остальные языки можно заполнять постепенно.</span></div>
            <ul class="nav nav-tabs" role="tablist">
                @foreach($locales as $locale)
                    <li class="nav-item" role="presentation"><button class="nav-link {{ $locale==='ru' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#section-lang-{{ $locale }}" type="button">{{ $languageLabels[$locale] }}</button></li>
                @endforeach
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4">
                @foreach($locales as $locale)
                    @php
                        $translation = $translations->get($locale);
                        $defaultTitle = $locale === 'ru' ? $section->title : optional($translation)->title;
                        $defaultDescription = $locale === 'ru' ? $section->description : optional($translation)->description;
                    @endphp
                    <div class="tab-pane fade {{ $locale==='ru' ? 'show active' : '' }}" id="section-lang-{{ $locale }}">
                        <div class="mb-3"><label class="form-label">Название — {{ $languageLabels[$locale] }}</label><input class="form-control" name="title_{{ $locale }}" value="{{ old('title_'.$locale,$defaultTitle) }}" {{ $locale==='ru' ? 'required' : '' }}></div>
                        <div><label class="form-label">Описание — {{ $languageLabels[$locale] }}</label><textarea class="form-control" rows="5" name="description_{{ $locale }}">{{ old('description_'.$locale,$defaultDescription) }}</textarea></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <button class="btn btn-dark btn-lg">Сохранить</button>
</form>

@if($section->exists)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h2 class="h5 mb-1">Связи с обучением</h2>
                <div class="text-muted small">Здесь не создаётся новый курс. Выберите уже существующий курс из списка и привяжите его к этому разделу / специальности.</div>
            </div>

            <form method="post" action="{{ route('admin.sections.assign-course',$section) }}" class="row g-2 align-items-end mb-4">
                @csrf
                <div class="col-lg-9">
                    <label class="form-label">Готовый учебный курс</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">— Выберите курс —</option>
                        @foreach($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}{{ $course->study_year ? ' · '.$course->study_year.' курс' : '' }}{{ $course->section ? ' · сейчас: '.$course->section->title : ' · без раздела' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3"><button class="btn btn-primary w-100">Привязать выбранный курс</button></div>
            </form>

            @if($availableCourses->isEmpty())
                <div class="alert alert-light border">Других готовых курсов для выбора сейчас нет.</div>
            @endif

            @if($section->courses->count())
                <h3 class="h6 mt-4">Курсы, привязанные непосредственно к этому разделу</h3>
                <div class="list-group mb-4">
                    @foreach($section->courses as $course)
                        <div class="list-group-item">
                            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                                <div><strong>{{ $course->title }}</strong><div class="small text-muted">{{ $course->study_year ? $course->study_year.' курс' : 'Курс не указан' }} · уроков: {{ $course->lessons->count() }}</div></div>
                                <div class="d-flex gap-2"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.courses.lessons.index',$course) }}">Уроки</a><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.courses.edit',$course) }}">Курс</a></div>
                            </div>
                            @if($course->lessons->count())
                                <div class="mt-2 small">@foreach($course->lessons as $lesson)<span class="badge text-bg-light border me-1 mb-1">{{ $lesson->title }}</span>@endforeach</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($section->children->count())
                <h3 class="h6 mt-4">Дочерние разделы / специальности</h3>
                <div class="accordion" id="sectionChildrenAccordion">
                    @foreach($section->children as $child)
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#child-{{ $child->id }}">{{ $child->localizedTitle('ru') }}<span class="badge text-bg-light ms-2">курсов: {{ $child->courses->count() }}</span></button></h2>
                            <div id="child-{{ $child->id }}" class="accordion-collapse collapse" data-bs-parent="#sectionChildrenAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-end mb-3"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.sections.edit',$child) }}">Редактировать раздел и выбрать готовый курс</a></div>
                                    @forelse($child->courses as $course)
                                        <div class="border rounded p-3 mb-2">
                                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                                <div><strong>{{ $course->title }}</strong><div class="small text-muted">{{ $course->study_year ? $course->study_year.' курс' : 'Курс не указан' }} · уроков: {{ $course->lessons->count() }}</div></div>
                                                <div class="d-flex gap-2"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.courses.lessons.index',$course) }}">Уроки</a><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.courses.edit',$course) }}">Курс</a></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted">К этому разделу пока не привязано ни одного учебного курса.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$section->courses->count())
                <div class="alert alert-light border mb-0">К этому разделу пока не привязаны курсы и нет дочерних разделов.</div>
            @endif
        </div>
    </div>
@endif
@endsection
