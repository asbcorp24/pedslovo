@extends('layouts.app')

@section('title','Проверка сертификата')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:760px">
        <div class="card-body p-4 p-md-5">
            <div class="text-success fw-bold mb-2">✓ Сертификат действителен</div>
            <h1 class="h3 mb-4">Проверка сертификата</h1>
            <dl class="row mb-0">
                <dt class="col-sm-4">Номер</dt><dd class="col-sm-8">{{ $certificate->number }}</dd>
                <dt class="col-sm-4">Выдан</dt><dd class="col-sm-8">{{ $certificate->issued_at->format('d.m.Y') }}</dd>
                <dt class="col-sm-4">Получатель</dt><dd class="col-sm-8">{{ $certificate->user->name }}</dd>
                <dt class="col-sm-4">Курс</dt><dd class="col-sm-8">{{ $certificate->course->title }}</dd>
                @if($certificate->score !== null)<dt class="col-sm-4">Итоговый балл</dt><dd class="col-sm-8">{{ $certificate->score }}</dd>@endif
            </dl>
        </div>
    </div>
</div>
@endsection
