@extends('admin.layout')

@section('title','Журнал — '.$course->title)

@section('content')
<a href="{{ route('admin.journal.index') }}" class="text-decoration-none">← Журнал</a>
<h1 class="mt-2">{{ $course->title }}</h1>
<form class="card admin-card shadow-sm p-3 my-3">
    <div class="row g-2">
        <div class="col-md-3"><select name="group_id" class="form-select"><option value="">Все группы</option>@foreach($groups as $g)<option value="{{ $g->id }}" @selected(request('group_id')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><input name="student" class="form-control" value="{{ request('student') }}" placeholder="Студент / email"></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">Любой статус</option>@foreach(['not_started','in_progress','completed','passed','failed'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col-md-3"><button class="btn btn-dark">Фильтр</button> <a href="{{ route('admin.journal.show',$course) }}" class="btn btn-light">Сбросить</a></div>
    </div>
</form>
<div class="card admin-card shadow-sm"><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th class="position-sticky start-0 bg-white">Студент</th>@foreach($course->lessons as $l)<th class="text-center" style="min-width:105px">{{ $loop->iteration }}<div class="small fw-normal text-muted">{{ Str::limit($l->title,18) }}</div></th>@endforeach</tr></thead><tbody>
@forelse($enrollments as $e)
<tr><td class="position-sticky start-0 bg-white"><strong>{{ $e->user->name }}</strong><div class="small text-muted">{{ $e->user->groups->pluck('name')->join(', ') }}</div></td>
@foreach($course->lessons as $l)
    @php($p=$progress->get($e->user_id.':'.$l->id))
    <td class="text-center">
        <span class="badge {{ $p && in_array($p->status,['passed','completed']) ? 'text-bg-success' : (($p && $p->status==='failed') ? 'text-bg-danger' : 'text-bg-light') }}">{{ $p && $p->score !== null ? $p->score : ($p ? $p->status : '—') }}</span>
    </td>
@endforeach
</tr>
@empty
<tr><td colspan="{{ $course->lessons->count()+1 }}" class="text-muted p-4">Нет студентов по выбранным фильтрам.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
