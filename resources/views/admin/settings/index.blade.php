@extends('admin.layout')

@section('title','Настройки сайта')

@section('content')
<div class="small-label">Система</div>
<h1>Настройки сайта и училища</h1>
<form method="post" action="{{ route('admin.settings.update') }}" class="mt-4">
    @csrf
    @method('PUT')
    <div class="card admin-card shadow-sm mb-4"><div class="card-body p-4"><h2 class="h5">Училище и контакты</h2><div class="row g-3">
        @foreach(['college_name'=>'Полное название','college_short_name'=>'Краткое название','college_site'=>'Официальный сайт','contact_phone'=>'Телефон','contact_email'=>'Email','address'=>'Адрес'] as $k=>$label)
            <div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $k }}" value="{{ old($k,$settings[$k] ?? '') }}"></div>
        @endforeach
    </div></div></div>
    <div class="card admin-card shadow-sm mb-4"><div class="card-body p-4"><h2 class="h5">Главная страница</h2><div class="row g-3">
        <div class="col-md-4"><label class="form-label">Метка над заголовком</label><input class="form-control" name="home_badge" value="{{ old('home_badge',$settings['home_badge'] ?? '') }}"></div>
        <div class="col-12"><label class="form-label">Главный заголовок</label><input class="form-control form-control-lg" name="home_title" value="{{ old('home_title',$settings['home_title'] ?? '') }}"></div>
        <div class="col-12"><label class="form-label">Подзаголовок</label><textarea class="form-control" rows="3" name="home_subtitle">{{ old('home_subtitle',$settings['home_subtitle'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">Заголовок блока «О проекте»</label><input class="form-control" name="home_about_title" value="{{ old('home_about_title',$settings['home_about_title'] ?? '') }}"></div>
        <div class="col-12"><label class="form-label">Текст блока «О проекте»</label><textarea class="form-control" rows="5" name="home_about_text">{{ old('home_about_text',$settings['home_about_text'] ?? '') }}</textarea></div>
    </div></div></div>
    <div class="card admin-card shadow-sm mb-4"><div class="card-body p-4"><h2 class="h5">Системные настройки</h2><div class="row g-3">
        <div class="col-12"><label class="form-label">Текст футера</label><textarea class="form-control" name="footer_text" rows="2">{{ old('footer_text',$settings['footer_text'] ?? '') }}</textarea></div>
        <div class="col-12"><label class="form-label">Код аналитики</label><textarea class="form-control font-monospace" name="analytics_code" rows="5">{{ old('analytics_code',$settings['analytics_code'] ?? '') }}</textarea></div>
        <div class="col-12"><label class="form-label">Системное уведомление</label><textarea class="form-control" name="maintenance_notice" rows="2">{{ old('maintenance_notice',$settings['maintenance_notice'] ?? '') }}</textarea></div>
    </div></div></div>
    <button class="btn btn-primary btn-lg">Сохранить настройки</button>
</form>
@endsection
