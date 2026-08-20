@extends('layouts.app')

@section('title', __('help.title'))

@section('content')
@php
    $roles = ['student','teacher','admin'];
@endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-uppercase small fw-bold gold mb-2">{{ __('help.current_role') }}: {{ __('help.'.$role.'.title') }}</div>
                    <h1 class="display-6 fw-bold mb-3">{{ __('help.heading') }}</h1>
                    <p class="lead text-muted mb-0">{{ __('help.intro') }}</p>
                </div>
            </div>

            <ul class="nav nav-pills gap-2 mb-4" id="helpTabs" role="tablist">
                @foreach($roles as $tabRole)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tabRole === $role ? 'active' : '' }}" id="help-{{ $tabRole }}-tab" data-bs-toggle="pill" data-bs-target="#help-{{ $tabRole }}" type="button" role="tab" aria-controls="help-{{ $tabRole }}" aria-selected="{{ $tabRole === $role ? 'true' : 'false' }}">
                            {{ __('help.'.$tabRole.'.title') }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($roles as $tabRole)
                    @php($items = __('help.'.$tabRole.'.items'))
                    <div class="tab-pane fade {{ $tabRole === $role ? 'show active' : '' }}" id="help-{{ $tabRole }}" role="tabpanel" aria-labelledby="help-{{ $tabRole }}-tab" tabindex="0">
                        <div class="mb-4">
                            <h2 class="h3">{{ __('help.'.$tabRole.'.title') }}</h2>
                            <p class="text-muted">{{ __('help.'.$tabRole.'.lead') }}</p>
                        </div>

                        <div class="accordion shadow-sm rounded-4 overflow-hidden" id="helpAccordion-{{ $tabRole }}">
                            @foreach($items as $item)
                                <div class="accordion-item">
                                    <h3 class="accordion-header" id="heading-{{ $tabRole }}-{{ $loop->index }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $tabRole }}-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $tabRole }}-{{ $loop->index }}">
                                            {{ $item['title'] }}
                                        </button>
                                    </h3>
                                    <div id="collapse-{{ $tabRole }}-{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $tabRole }}-{{ $loop->index }}" data-bs-parent="#helpAccordion-{{ $tabRole }}">
                                        <div class="accordion-body">{{ $item['text'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                @auth
                    <a href="{{ route('cabinet') }}" class="btn btn-wine">{{ __('help.open_cabinet') }}</a>
                    @if(auth()->user()->canEditContent())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">{{ __('help.open_admin') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
