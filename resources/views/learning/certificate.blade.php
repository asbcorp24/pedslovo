@extends('layouts.app')

@section('title',__('Сертификат').' — '.$certificate->course->title)

@section('content')
@php
    $template = $certificate->template;
    $verifyUrl = $certificate->verification_token ? route('certificates.verify',$certificate->verification_token) : null;
    $body = $template && $template->body_text
        ? strtr($template->body_text,[
            '{student}'=>$certificate->user->name,
            '{course}'=>$certificate->course->title,
            '{score}'=>$certificate->score !== null ? $certificate->score : '—',
            '{number}'=>$certificate->number,
            '{date}'=>$certificate->issued_at->format('d.m.Y'),
        ])
        : null;
@endphp
<div class="container py-5">
    <div class="d-flex justify-content-end mb-3"><button class="btn btn-outline-dark" onclick="window.print()">Печать</button></div>
    <div class="mx-auto bg-white border rounded-4 p-5 text-center shadow-sm position-relative overflow-hidden" style="max-width:1000px;min-height:680px;{{ $template && $template->background_path ? 'background:url('.asset('storage/'.$template->background_path).') center/cover no-repeat;' : '' }}">
        <div class="position-relative" style="z-index:2">
            <div class="text-uppercase text-muted">{{ __('Цифровой образовательный ресурс «Педслово»') }}</div>
            <h1 class="display-5 mt-4">{{ $template && $template->title ? $template->title : __('СЕРТИФИКАТ') }}</h1>
            @if($body)
                <div class="lead mt-5" style="white-space:pre-line">{{ $body }}</div>
            @else
                <p class="lead mt-4">{{ __('Настоящим подтверждается, что') }}</p>
                <h2>{{ $certificate->user->name }}</h2>
                <p class="lead">{{ __('успешно завершил(а) курс') }}</p>
                <h3>«{{ $certificate->course->title }}»</h3>
            @endif
            @if((!$template || $template->show_score) && $certificate->score !== null)<p class="mt-3">{{ __('Итоговый балл:') }} {{ $certificate->score }}</p>@endif

            @if($template && ($template->signer_name || $template->signature_path || $template->stamp_path))
                <div class="row align-items-end mt-5 text-start">
                    <div class="col-md-7">
                        @if($template->signer_position)<div class="small text-muted">{{ $template->signer_position }}</div>@endif
                        @if($template->signer_name)<strong>{{ $template->signer_name }}</strong>@endif
                        @if($template->signature_path)<div><img src="{{ asset('storage/'.$template->signature_path) }}" alt="Подпись" style="max-height:70px;max-width:220px"></div>@endif
                    </div>
                    <div class="col-md-5 text-md-end">@if($template->stamp_path)<img src="{{ asset('storage/'.$template->stamp_path) }}" alt="Печать" style="max-height:110px;max-width:150px">@endif</div>
                </div>
            @endif

            <div class="row mt-5 text-start align-items-end">
                <div class="col">№ {{ $certificate->number }}<br><span class="small text-muted">{{ $certificate->issued_at->format('d.m.Y') }}</span></div>
                @if($verifyUrl && (!$template || $template->show_qr))
                    <div class="col text-end"><img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($verifyUrl) }}" width="120" height="120" alt="QR-код проверки"><div class="small text-muted">Проверка подлинности</div></div>
                @endif
            </div>
        </div>
    </div>
</div>
<style>@media print{nav,.topline,.footer,.a11y-bar,.container>.d-flex{display:none!important}body{background:#fff!important}.container{max-width:none!important;padding:0!important}.shadow-sm{box-shadow:none!important}.border{border:0!important}}</style>
@endsection
