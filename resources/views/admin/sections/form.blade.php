@extends('admin.layout')

@section('title',$section->exists ? 'Изменить раздел' : 'Новый раздел')

@section('content')
<h1 class="mb-4">{{ $section->exists ? 'Изменить раздел' : 'Новый раздел' }}</h1>
<form method="post" action="{{ $section->exists ? route('admin.sections.update',$section) : route('admin.sections.store') }}">
    @csrf
    @if($section->exists)
        @method('PUT')
    @endif
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="mb-3"><label class="form-label">Название</label><input class="form-control" name="title" value="{{ old('title',$section->title) }}" required></div>
            <div class="mb-3"><label class="form-label">Родительский раздел</label><select class="form-select" name="parent_id"><option value="">Нет</option>@foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id',$section->parent_id)==$p->id)>{{ $p->title }}</option>@endforeach</select></div>
            <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Тип</label><input class="form-control" name="type" value="{{ old('type',$section->type) }}" placeholder="audience, specialty, course, topic"></div><div class="col-md-6 mb-3"><label class="form-label">Порядок</label><input class="form-control" type="number" name="sort_order" value="{{ old('sort_order',$section->sort_order ?? 0) }}"></div></div>
            <div class="mb-3"><label class="form-label">Описание</label><textarea class="form-control" rows="5" name="description">{{ old('description',$section->description) }}</textarea></div>
            <div class="form-check mb-3"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$section->exists ? $section->is_active : true))><label class="form-check-label">Активен</label></div>
            <button class="btn btn-dark">Сохранить</button>
        </div>
    </div>
</form>
@endsection
