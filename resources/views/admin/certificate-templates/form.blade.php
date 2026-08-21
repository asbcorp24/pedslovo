@extends('admin.layout')

@section('title',$template->exists ? 'Шаблон сертификата' : 'Новый шаблон сертификата')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><div class="small-label">Учебная часть</div><h1 class="mb-0">{{ $template->exists ? 'Шаблон сертификата' : 'Новый шаблон сертификата' }}</h1></div>
    <a href="{{ route('admin.certificate-templates.index') }}" class="btn btn-light">← К списку</a>
</div>

<form method="post" enctype="multipart/form-data" action="{{ $template->exists ? route('admin.certificate-templates.update',$template) : route('admin.certificate-templates.store') }}">
    @csrf
    @if($template->exists) @method('PUT') @endif

    <div class="card admin-card shadow-sm mb-4"><div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Название шаблона</label><input class="form-control" name="name" required value="{{ old('name',$template->name) }}"></div>
            <div class="col-md-4"><label class="form-label">Язык</label><select class="form-select" name="locale">@foreach(['ru'=>'Русский','cv'=>'Чувашский','mhr'=>'Марийский','tt'=>'Татарский'] as $k=>$v)<option value="{{ $k }}" @selected(old('locale',$template->locale ?: 'ru')===$k)>{{ $v }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Заголовок</label><input class="form-control" name="title" value="{{ old('title',$template->title ?: 'СЕРТИФИКАТ') }}"></div>
            <div class="col-12"><label class="form-label">Текст сертификата</label><textarea class="form-control" rows="4" name="body_text" placeholder="Настоящим подтверждается, что {student} успешно завершил(а) курс {course}">{{ old('body_text',$template->body_text) }}</textarea><div class="form-text">Переменные: <code>{student}</code>, <code>{course}</code>, <code>{score}</code>, <code>{number}</code>, <code>{date}</code>.</div></div>
            <div class="col-md-6"><label class="form-label">ФИО подписанта</label><input class="form-control" name="signer_name" value="{{ old('signer_name',$template->signer_name) }}"></div>
            <div class="col-md-6"><label class="form-label">Должность подписанта</label><input class="form-control" name="signer_position" value="{{ old('signer_position',$template->signer_position) }}"></div>
            <div class="col-md-4"><label class="form-label">Фон A4</label><input type="file" class="form-control" name="background" accept="image/*">@if($template->background_path)<div class="small mt-1">Загружен: {{ basename($template->background_path) }}</div>@endif</div>
            <div class="col-md-4"><label class="form-label">Подпись</label><input type="file" class="form-control" name="signature" accept="image/*">@if($template->signature_path)<div class="small mt-1">Загружена</div>@endif</div>
            <div class="col-md-4"><label class="form-label">Печать</label><input type="file" class="form-control" name="stamp" accept="image/*">@if($template->stamp_path)<div class="small mt-1">Загружена</div>@endif</div>
        </div>
        <div class="d-flex flex-wrap gap-4 mt-4">
            <div class="form-check"><input type="hidden" name="show_score" value="0"><input class="form-check-input" type="checkbox" name="show_score" value="1" @checked(old('show_score',$template->exists ? $template->show_score : true))><label class="form-check-label">Показывать итоговый балл</label></div>
            <div class="form-check"><input type="hidden" name="show_qr" value="0"><input class="form-check-input" type="checkbox" name="show_qr" value="1" @checked(old('show_qr',$template->exists ? $template->show_qr : true))><label class="form-check-label">Показывать QR проверки</label></div>
            <div class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$template->exists ? $template->is_active : true))><label class="form-check-label">Активен</label></div>
        </div>
    </div></div>

    <button class="btn btn-primary btn-lg">Сохранить шаблон</button>
</form>
@endsection
