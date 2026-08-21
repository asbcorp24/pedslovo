@extends('admin.layout')

@section('title','Сертификаты')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><div class="small-label">Учебная часть</div><h1 class="mb-0">Шаблоны сертификатов</h1></div>
    <a class="btn btn-primary" href="{{ route('admin.certificate-templates.create') }}">+ Новый шаблон</a>
</div>

<div class="card admin-card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Название</th><th>Язык</th><th>Курсов</th><th>Статус</th><th></th></tr></thead>
            <tbody>
            @forelse($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>{{ strtoupper($template->locale) }}</td>
                    <td>{{ $template->courses_count }}</td>
                    <td><span class="badge {{ $template->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $template->is_active ? 'Активен' : 'Выключен' }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.certificate-templates.edit',$template) }}">Изменить</a>
                        <form class="d-inline" method="post" action="{{ route('admin.certificate-templates.destroy',$template) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить шаблон?')">Удалить</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">Шаблонов пока нет.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
