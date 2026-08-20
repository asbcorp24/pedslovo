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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Slug</label>
                    <input class="form-control" name="slug" value="{{ old('slug',$section->slug) }}" placeholder="создастся из русского названия">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Тип</label>
                    <input class="form-control" name="type" value="{{ old('type',$section->type) }}" placeholder="audience, specialty, program">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Порядок</label>
                    <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order',$section->sort_order ?? 0) }}">
                </div>
            </div>
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$section->exists ? $section->is_active : true))>
                <label class="form-check-label">Активен</label>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="h5 mb-0">Название и описание на языках</h2>
                <span class="text-muted small">Русский обязателен, остальные языки можно заполнять постепенно.</span>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                @foreach($locales as $locale)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $locale==='ru' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#section-lang-{{ $locale }}" type="button">
                            {{ $languageLabels[$locale] }}
                        </button>
                    </li>
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
                        <div class="mb-3">
                            <label class="form-label">Название — {{ $languageLabels[$locale] }}</label>
                            <input class="form-control" name="title_{{ $locale }}" value="{{ old('title_'.$locale,$defaultTitle) }}" {{ $locale==='ru' ? 'required' : '' }}>
                        </div>
                        <div>
                            <label class="form-label">Описание — {{ $languageLabels[$locale] }}</label>
                            <textarea class="form-control" rows="5" name="description_{{ $locale }}">{{ old('description_'.$locale,$defaultDescription) }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <button class="btn btn-dark btn-lg">Сохранить</button>
</form>
@endsection
