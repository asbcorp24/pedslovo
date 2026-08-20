@extends('layouts.app')

@section('title',__('Вход'))

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:430px">
        <div class="card-body p-4">
            <h1 class="h3 mb-4">{{ __('Вход') }}</h1>
            <form method="post" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3"><label class="form-label">{{ __('Пароль') }}</label><input class="form-control" type="password" name="password" required></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label" for="remember">{{ __('Запомнить меня') }}</label></div>
                <button class="btn btn-dark w-100">{{ __('Вход') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
