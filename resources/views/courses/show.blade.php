@extends('layouts.app')

@section('title',$course->title.' — Педслово')

@section('content')
<div class="container py-5">
    <a href="{{ url()->previous() }}" class="text-decoration-none">← {{ __('Назад') }}</a>
    <div class="row mt-3">
        <div class="col-lg-8"><h1>{{ $course->title }}</h1><p class="lead">{{ $course->description }}</p></div>
        <div class="col-lg-4 text-lg-end">
            @auth
                @if(!$enrollment)
                    <form method="post" action="{{ route('courses.enroll',$course) }}">@csrf<button class="btn btn-primary btn-lg">{{ __('Записаться на курс') }}</button></form>
                @else
                    <span class="badge bg-success p-3">{{ $enrollment->status==='completed' ? __('Курс завершён') : __('Вы записаны') }}</span>
                @endif
            @endauth
        </div>
    </div>
    <hr>
    <h3>{{ __('Программа курса') }}</h3>
    <div class="list-group mt-3">
        @forelse($course->lessons as $lesson)
            <div class="list-group-item py-3"><div class="d-flex justify-content-between"><strong>{{ $loop->iteration }}. {{ $lesson->title }}</strong><span class="badge bg-secondary">{{ $lesson->lesson_type }}</span></div><p class="mb-2 text-muted">{{ $lesson->description }}</p>
                @auth
                    @if($enrollment || auth()->user()->isAdmin())
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('learning.lesson',$lesson) }}">{{ __('Открыть урок') }}</a>
                    @else
                        <span class="small text-muted">{{ __('Запишитесь на курс, чтобы открыть урок.') }}</span>
                    @endif
                @else
                    <span class="small text-muted">{{ __('Войдите, чтобы начать обучение.') }}</span>
                @endauth
            </div>
        @empty
            <div class="alert alert-light">{{ __('Материалы курса пока не добавлены.') }}</div>
        @endforelse
    </div>
</div>
@endsection
