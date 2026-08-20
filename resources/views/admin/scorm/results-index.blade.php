@extends('admin.layout')

@section('title','Результаты SCORM')

@section('content')
<div class="small-label">iSpring / SCORM</div>
<h1>Результаты по студентам</h1>
<form class="my-3"><div class="input-group" style="max-width:520px"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="ФИО или email"><button class="btn btn-dark">Найти</button></div></form>
<div class="card admin-card shadow-sm"><table class="table align-middle mb-0"><thead><tr><th>Студент</th><th>Попыток</th><th></th></tr></thead><tbody>
@foreach($users as $u)
<tr><td><strong>{{ $u->name }}</strong><div class="small text-muted">{{ $u->email }}</div></td><td>{{ $u->scorm_attempts_count }}</td><td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('admin.scorm-results.show',$u) }}">Все результаты</a></td></tr>
@endforeach
</tbody></table></div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
