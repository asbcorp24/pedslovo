@extends('admin.layout')

@section('title','Настройки сайта')

@section('content')
<div class="small-label">Система</div>
<h1>Настройки сайта и училища</h1>
<form method="post" action="{{ route('admin.settings.update') }}" class="mt-4">
    @csrf
    @method('PUT')

    <div class="card admin-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5">Училище и контакты</h2>
            <div class="row g-3">
                @foreach(['college_name'=>'Полное название','college_short_name'=>'Краткое название','college_site'=>'Официальный сайт','contact_phone'=>'Телефон','contact_email'=>'Email','address'=>'Адрес'] as $k=>$label)
                    <div class="col-md-6">
                        <label class="form-label">{{ $label }}</label>
                        <input class="form-control" name="{{ $k }}" value="{{ old($k,$settings[$k] ?? '') }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card admin-card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Главная страница</h2>
                    <div class="text-muted small">Все тексты ниже хранятся в БД отдельно для каждого языка.</div>
                </div>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                @foreach($locales as $locale=>$label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#home-{{ $locale }}" type="button" role="tab">
                            {{ $label }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4">
                @foreach($locales as $locale=>$label)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="home-{{ $locale }}" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Метка над заголовком</label>
                                <input class="form-control" name="home_badge_{{ $locale }}" value="{{ old('home_badge_'.$locale,$settings['home_badge_'.$locale] ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Главный заголовок</label>
                                <input class="form-control form-control-lg" name="home_title_{{ $locale }}" value="{{ old('home_title_'.$locale,$settings['home_title_'.$locale] ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Подзаголовок</label>
                                <textarea class="form-control" rows="3" name="home_subtitle_{{ $locale }}">{{ old('home_subtitle_'.$locale,$settings['home_subtitle_'.$locale] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Заголовок блока «О проекте»</label>
                                <input class="form-control" name="home_about_title_{{ $locale }}" value="{{ old('home_about_title_'.$locale,$settings['home_about_title_'.$locale] ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Текст блока «О проекте»</label>
                                <textarea class="form-control" rows="5" name="home_about_text_{{ $locale }}">{{ old('home_about_text_'.$locale,$settings['home_about_text_'.$locale] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card admin-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5">Системные настройки</h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Текст футера</label><textarea class="form-control" name="footer_text" rows="2">{{ old('footer_text',$settings['footer_text'] ?? '') }}</textarea></div>
                <div class="col-12"><label class="form-label">Код аналитики</label><textarea class="form-control font-monospace" name="analytics_code" rows="5">{{ old('analytics_code',$settings['analytics_code'] ?? '') }}</textarea></div>
                <div class="col-12"><label class="form-label">Системное уведомление</label><textarea class="form-control" name="maintenance_notice" rows="2">{{ old('maintenance_notice',$settings['maintenance_notice'] ?? '') }}</textarea></div>
            </div>
        </div>
    </div>

    <button class="btn btn-primary btn-lg">Сохранить настройки</button>
</form>
@endsection
