@extends('layouts.app')

@section('title',$material->title)

@section('content')
<article class="container py-5"><div class="mx-auto" style="max-width:900px">
    <span class="badge text-bg-light">{{ $material->material_type }}</span>
    <h1 class="display-6 fw-bold mt-3">{{ $material->title }}</h1>
    @if($material->author)
        <div class="text-muted mb-3">{{ __('Автор:') }} {{ $material->author }}</div>
    @endif
    @if($material->annotation)
        <p class="lead">{{ $material->annotation }}</p>
    @endif
    <div class="bg-white rounded-4 shadow-sm p-4">{!! $material->content !!}</div>
    @if($material->media_url)
        <a class="btn btn-dark mt-3" href="{{ $material->media_url }}" target="_blank" rel="noopener">{{ __('Открыть материал') }}</a>
    @endif
</div></article>
@endsection
