@extends('admin.layout')

@section('title','Учётные данные студентов')

@section('content')
<style>
@media print {
    .sidebar, .no-print { display:none !important; }
    main { width:100% !important; padding:0 !important; }
    .credential-card { break-inside:avoid; box-shadow:none !important; border:1px solid #999 !important; }
}
.credential-card { border-radius:14px; }
.credential-value { font-family:monospace; font-size:1.05rem; }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4 no-print">
    <div><a href="{{ route('admin.users.index') }}" class="text-decoration-none">← Пользователи</a><h1 class="mt-2 mb-0">Логины и пароли студентов</h1></div>
    <button class="btn btn-dark" onclick="window.print()">Печать</button>
</div>

<form class="card admin-card shadow-sm p-3 mb-4 no-print" method="get">
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="ФИО или email"></div>
        <div class="col-md-4"><select class="form-select" name="group_id"><option value="">Все группы</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected((string)$selectedGroup===(string)$g->id)>{{ $g->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><button class="btn btn-primary">Показать</button> <a class="btn btn-light" href="{{ route('admin.users.credentials') }}">Сбросить</a></div>
    </div>
</form>

<div class="alert alert-warning no-print">Пароли отображаются только администратору. Не оставляйте распечатки в открытом доступе. Если пароль для старой учётной записи не сохранён, задайте студенту новый пароль в карточке пользователя.</div>

<div class="row g-3">
@forelse($students as $student)
    <div class="col-md-6">
        <div class="card credential-card shadow-sm h-100">
            <div class="card-body p-4">
                <div class="small text-muted">{{ $student->groups->pluck('name')->join(', ') ?: 'Без группы' }}</div>
                <h2 class="h5 mt-1">{{ $student->name }}</h2>
                <hr>
                <div class="mb-2"><span class="text-muted">Логин:</span> <span class="credential-value">{{ $student->email }}</span></div>
                <div><span class="text-muted">Пароль:</span> <span class="credential-value">{{ $student->visible_password ?: 'НЕ СОХРАНЁН' }}</span></div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-light border">Студенты по выбранным условиям не найдены.</div></div>
@endforelse
</div>
@endsection
