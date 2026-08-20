@extends('layouts.app')

@section('title',__('Мои сертификаты — Педслово'))

@section('content')
<div class="container py-5">
    <h1>{{ __('Мои сертификаты') }}</h1>
    <div class="row g-3 mt-2">
        @forelse($certificates as $c)
            <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">{{ $c->number }}</div><h4>{{ $c->course->title }}</h4><div>{{ __('Выдан:') }} {{ $c->issued_at->format('d.m.Y') }}</div>
                @if($c->score !== null)
                    <div>{{ __('Средний балл:') }} {{ $c->score }}</div>
                @endif
                <a class="btn btn-outline-primary mt-3" href="{{ route('certificates.show',$c) }}">{{ __('Открыть сертификат') }}</a></div></div></div>
        @empty
            <div class="alert alert-light">{{ __('Сертификатов пока нет.') }}</div>
        @endforelse
    </div>
</div>
@endsection
