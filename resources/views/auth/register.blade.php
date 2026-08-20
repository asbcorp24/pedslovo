@extends('layouts.app')

@section('title','Регистрация')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:560px">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-2">Регистрация</h1>
            <p class="text-muted mb-4">После отправки заявки аккаунт должен подтвердить администратор. До подтверждения вход будет недоступен.</p>

            <form method="post" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">ФИО</label>
                    <input class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Кто вы</label>
                    <select class="form-select" name="role" required>
                        <option value="student" @selected(old('role','student')==='student')>Студент</option>
                        <option value="teacher" @selected(old('role')==='teacher')>Преподаватель</option>
                    </select>
                    @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Пароль</label>
                        <input class="form-control" type="password" name="password" minlength="8" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Повторите пароль</label>
                        <input class="form-control" type="password" name="password_confirmation" minlength="8" required>
                    </div>
                </div>
                @error('password')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                <button class="btn btn-wine w-100 mt-4" type="submit">Отправить заявку</button>
                <div class="text-center mt-3"><a href="{{ route('login') }}">Уже есть аккаунт? Войти</a></div>
            </form>
        </div>
    </div>
</div>
@endsection
