@extends('admin.layout')

@section('title',$user->exists ? 'Пользователь' : 'Новый пользователь')

@section('content')
<div class="mb-4"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">← Пользователи</a><h1 class="mt-2">{{ $user->exists ? 'Редактирование пользователя' : 'Новый пользователь' }}</h1></div>

@if($user->exists && $user->role === 'student')
    <div class="card admin-card shadow-sm mb-4 border-warning-subtle">
        <div class="card-body">
            <div class="small-label">Учётные данные студента</div>
            <div class="row g-3 align-items-end mt-1">
                <div class="col-md-5"><label class="form-label">Логин</label><input class="form-control" readonly value="{{ $user->email }}"></div>
                <div class="col-md-5"><label class="form-label">Текущий пароль</label><input id="studentPassword" class="form-control font-monospace" readonly type="password" value="{{ $visiblePassword ?: '' }}" placeholder="Пароль ранее не сохранялся"></div>
                <div class="col-md-2"><button type="button" class="btn btn-outline-dark w-100" onclick="var x=document.getElementById('studentPassword');x.type=x.type==='password'?'text':'password'">Показать</button></div>
            </div>
            @if(!$visiblePassword)
                <div class="form-text mt-2">Старый пароль восстановить из хеша нельзя. Задайте новый пароль ниже — после сохранения администратор сможет его посмотреть и распечатать.</div>
            @endif
        </div>
    </div>
@endif

<form method="post" action="{{ $user->exists ? route('admin.users.update',$user) : route('admin.users.store') }}" class="card admin-card shadow-sm">
    <div class="card-body p-4">
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">ФИО</label><input name="name" class="form-control" required value="{{ old('name',$user->name) }}"></div>
            <div class="col-md-6"><label class="form-label">Email / логин</label><input type="email" name="email" class="form-control" required value="{{ old('email',$user->email) }}"></div>
            <div class="col-md-6"><label class="form-label">Роль</label><select name="role" class="form-select" required>@foreach(['student'=>'Студент','teacher'=>'Преподаватель','editor'=>'Редактор контента','admin'=>'Администратор'] as $k=>$v)<option value="{{ $k }}" @selected(old('role',$user->role ?: 'student')===$k)>{{ $v }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Пароль {{ $user->exists ? '(оставьте пустым, чтобы не менять)' : '(можно оставить пустым — сгенерируется)' }}</label><input type="text" name="password" class="form-control font-monospace" minlength="8" autocomplete="new-password"><div class="form-text">Для студентов пароль хранится в двух формах: безопасный хеш для входа и отдельно зашифрованная копия для выдачи администратором.</div></div>
            <div class="col-12"><label class="form-label">Учебные группы</label><div class="row g-2">@foreach($groups as $g)<div class="col-md-4"><label class="border rounded p-2 d-block"><input type="checkbox" name="group_ids[]" value="{{ $g->id }}" @checked(in_array($g->id,old('group_ids',$user->exists ? $user->groups()->pluck('groups.id')->all() : [])))> {{ $g->name }}</label></div>@endforeach</div></div>
        </div>
    </div>
    <div class="card-footer bg-white border-0 p-4 pt-0">
        <button class="btn btn-primary">Сохранить</button>
        @if($user->exists && $user->role === 'student')
            <a class="btn btn-outline-dark ms-2" target="_blank" href="{{ route('admin.users.credentials',['ids'=>$user->id]) }}">Печать логина и пароля</a>
        @endif
        @if($user->exists && $user->id !== auth()->id())
            <button form="delete-user" type="button" onclick="if(confirm('Удалить пользователя?'))document.getElementById('delete-user').submit()" class="btn btn-outline-danger ms-2">Удалить</button>
        @endif
    </div>
</form>
@if($user->exists && $user->id !== auth()->id())
<form id="delete-user" method="post" action="{{ route('admin.users.destroy',$user) }}">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
