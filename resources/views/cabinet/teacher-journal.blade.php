@extends('layouts.app')

@section('title',__('Журнал успеваемости').' — '.$course->title)

@section('content')
<div class="container-fluid px-lg-5 py-5">
    <a href="{{ route('cabinet') }}" class="text-decoration-none">← {{ __('Кабинет преподавателя') }}</a>
    <div class="eyebrow mt-3">{{ __('Журнал успеваемости') }}</div>
    <h1 class="h2">{{ $course->title }}</h1>
    <div class="card card-soft mt-4"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="p-3">{{ __('Студент') }}</th>
        @foreach($course->lessons as $lesson)
            <th class="small">{{ $loop->iteration }}. {{ $lesson->title }}</th>
        @endforeach
    </tr></thead><tbody>
        @forelse($enrollments as $e)
            <tr><td class="p-3 fw-semibold">{{ $e->user->name }}</td>
            @foreach($course->lessons as $lesson)
                @php($p=$progress->get($e->user_id.':'.$lesson->id))
                <td>
                    @if($p)
                        <span class="badge {{ in_array($p->status,['completed','passed']) ? 'bg-success' : ($p->status==='failed' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $p->status }}</span>
                        @if($p->score !== null)
                            <div class="small mt-1">{{ $p->score }}</div>
                        @endif
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ $course->lessons->count()+1 }}" class="p-4 text-muted">{{ __('На курс ещё никто не записан.') }}</td></tr>
        @endforelse
    </tbody></table></div></div>
</div>
@endsection
