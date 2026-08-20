@extends('admin.layout')

@section('title','Админ-панель')

@section('content')
<div class="mb-4">
    <div class="text-uppercase small text-muted fw-bold">Учебная часть</div>
    <h1 class="h2">Панель управления «Педслово»</h1>
</div>

<div class="row g-3">
    @foreach([
        ['Разделы',$sections,'admin.sections.index'],
        ['Материалы',$materials,'admin.materials.index'],
        ['Курсы',$courses,'admin.courses.index'],
        ['Группы',$groups,'admin.groups.index'],
        ['Пользователи',$users,'admin.users.index'],
        ['SEO',$seoPages,'admin.seo.index']
    ] as $item)
        <div class="col-md-6 col-xl-4">
            <a class="text-decoration-none text-dark" href="{{ route($item[2]) }}">
                <div class="card admin-card h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-muted">{{ $item[0] }}</div>
                        <div class="display-6 fw-semibold">{{ $item[1] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card admin-card mt-4 shadow-sm">
    <div class="card-body p-4">
        <h2 class="h5">Быстрые действия</h2>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="{{ route('admin.materials.create') }}">Новый материал</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.courses.create') }}">Новый курс</a>
            <a class="btn btn-outline-dark" href="{{ route('admin.seo.create') }}">Настроить SEO</a>
            @if(auth()->user()->role === 'admin')
                <a class="btn btn-outline-dark" href="{{ route('admin.journal.index') }}">Открыть журнал</a>
            @endif
        </div>
    </div>
</div>
@endsection
