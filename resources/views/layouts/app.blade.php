<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $seoTitle = isset($seoPage) && $seoPage && $seoPage->title ? $seoPage->title : 'Педслово — ЧМУ им. Ф.П. Павлова';
        $seoDescription = isset($seoPage) && $seoPage ? $seoPage->description : null;
        $seoKeywords = isset($seoPage) && $seoPage ? $seoPage->keywords : null;
        $seoRobots = isset($seoPage) && $seoPage && $seoPage->robots ? $seoPage->robots : 'index,follow';
        $seoCanonical = isset($seoPage) && $seoPage && $seoPage->canonical_url ? $seoPage->canonical_url : url()->current();
        $seoOgTitle = isset($seoPage) && $seoPage && $seoPage->og_title ? $seoPage->og_title : $seoTitle;
        $seoOgDescription = isset($seoPage) && $seoPage ? $seoPage->og_description : $seoDescription;
        $seoOgImage = isset($seoPage) && $seoPage ? $seoPage->og_image : null;
    @endphp

    <title>@yield('title', $seoTitle)</title>
    @if($seoDescription)<meta name="description" content="{{ $seoDescription }}">@endif
    @if($seoKeywords)<meta name="keywords" content="{{ $seoKeywords }}">@endif
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoOgTitle }}">
    @if($seoOgDescription)<meta property="og:description" content="{{ $seoOgDescription }}">@endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seoOgImage)<meta property="og:image" content="{{ $seoOgImage }}">@endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--wine:#6f1d35;--wine2:#42101f;--gold:#c9a34e;--paper:#f7f3ee}html,body{min-height:100%}body{min-height:100vh;display:flex;flex-direction:column;background:var(--paper);color:#261d1d}main{flex:1 0 auto}.footer{margin-top:auto!important;background:#27161c;color:#d8c8c1}.navbar-brand{font-weight:800;letter-spacing:.02em}.topline{background:var(--wine2);color:#eaded4;font-size:.85rem;position:relative;z-index:1040}.topline .dropdown-menu{z-index:1060}.hero{background:linear-gradient(120deg,#5b1429,#862847);color:#fff;border-radius:28px;overflow:hidden;position:relative}.hero:after{content:'♫';position:absolute;right:4%;top:-15%;font-size:240px;color:rgba(255,255,255,.06)}.section-card,.content-card{border:0;border-radius:20px;transition:.2s}.section-card:hover,.content-card:hover{transform:translateY(-4px);box-shadow:0 15px 35px rgba(57,24,24,.12)}.gold{color:var(--gold)}.btn-wine{background:var(--wine);color:#fff;border-color:var(--wine)}.btn-wine:hover{background:var(--wine2);color:#fff}.year-pill{border:1px solid #ead7c2;border-radius:999px;padding:.35rem .7rem;background:#fff}.main-nav .nav-link{font-weight:600;color:#49383d;border-radius:.6rem;padding:.5rem .7rem}.main-nav .nav-link:hover,.main-nav .nav-link.active{background:#f5ecef;color:var(--wine)}.user-chip{max-width:190px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.a11y-bar{display:none;background:#fff;border-bottom:2px solid #000;padding:.7rem 0}.a11y .a11y-bar{display:block}.a11y{font-size:20px;line-height:1.65;background:#fff!important;color:#000!important}.a11y *{text-shadow:none!important}.a11y .shadow,.a11y .shadow-sm,.a11y .section-card,.a11y .content-card{box-shadow:none!important}.a11y a{text-decoration:underline!important}.a11y :focus{outline:3px solid #ffbf00!important;outline-offset:2px}.a11y-high{background:#000!important;color:#fff!important}.a11y-high .bg-white,.a11y-high .card,.a11y-high .navbar,.a11y-high .dropdown-menu,.a11y-high .a11y-bar{background:#000!important;color:#fff!important;border-color:#fff!important}.a11y-high a,.a11y-high .nav-link,.a11y-high .text-muted,.a11y-high .text-dark{color:#fff!important}.a11y-hide-images img,.a11y-hide-images picture,.a11y-hide-images .hero:after{display:none!important}@media(max-width:991.98px){.navbar-collapse{padding:1rem 0}.navbar-actions{border-top:1px solid #eee;padding-top:1rem;margin-top:.6rem}.topline .container{gap:.5rem;font-size:.76rem}}
    </style>
</head>
<body>
@php
    $college = \App\Models\SiteSetting::value('college_short_name', 'ЧМУ им. Ф.П. Павлова');
    $collegeSite = \App\Models\SiteSetting::value('college_site', 'https://xn--g1ajvbu.xn--p1ai/');
    $notice = \App\Models\SiteSetting::value('maintenance_notice');
    $footerText = \App\Models\SiteSetting::value('footer_text','Образовательный ресурс Чебоксарского музыкального училища имени Ф.П. Павлова');
    $contactPhone = \App\Models\SiteSetting::value('contact_phone');
    $contactEmail = \App\Models\SiteSetting::value('contact_email');
    $analyticsCode = \App\Models\SiteSetting::value('analytics_code', '');
@endphp

<div class="a11y-bar" id="a11yBar">
    <div class="container d-flex flex-wrap gap-2 align-items-center">
        <strong>{{ __('ui.accessibility') }}</strong>
        <button type="button" class="btn btn-outline-dark btn-sm" data-a11y-size="down">A−</button>
        <button type="button" class="btn btn-outline-dark btn-sm" data-a11y-size="up">A+</button>
        <button type="button" class="btn btn-outline-dark btn-sm" id="a11yContrast">{{ __('ui.contrast') }}</button>
        <button type="button" class="btn btn-outline-dark btn-sm" id="a11yImages">{{ __('ui.images') }}</button>
        <button type="button" class="btn btn-dark btn-sm ms-auto" id="a11yOff">{{ __('ui.normal_view') }}</button>
    </div>
</div>

<div class="topline py-2">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>{{ __('Учебная часть') }} {{ $college }}</span>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button type="button" class="btn btn-sm btn-outline-light" id="a11yOn">{{ __('ui.accessibility') }}</button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ __('ui.language') }}: {{ strtoupper(app()->getLocale()) }}</button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="{{ route('locale.switch','ru') }}">{{ __('ui.russian') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('locale.switch','cv') }}">{{ __('ui.chuvash') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('locale.switch','mhr') }}">{{ __('ui.mari') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('locale.switch','tt') }}">{{ __('ui.tatar') }}</a></li>
                </ul>
            </div>
            <a class="text-white-50 text-decoration-none" href="{{ $collegeSite }}" target="_blank" rel="noopener">{{ __('ui.official_site') }} ↗</a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand me-4" href="{{ route('home') }}"><span class="gold">♪</span> ПЕДСЛОВО</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="{{ __('Открыть меню') }}"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav main-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('ui.home') }}</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('learning.my-courses') || request()->routeIs('learning.lesson') ? 'active' : '' }}" href="{{ route('learning.my-courses') }}">{{ __('ui.my_courses') }}</a></li>
                    @if(Route::has('certificates.index'))<li class="nav-item"><a class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" href="{{ route('certificates.index') }}">{{ __('ui.certificates') }}</a></li>@endif
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('help') ? 'active' : '' }}" href="{{ route('help') }}">{{ __('ui.help') }}</a></li>
                @endauth
            </ul>
            <div class="navbar-actions d-flex flex-wrap gap-2 align-items-center">
                @auth
                    <span class="small text-muted user-chip" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                    <a href="{{ route('cabinet') }}" class="btn btn-light btn-sm">{{ __('ui.cabinet') }}</a>
                    @if(auth()->user()->canEditContent())<a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm">{{ __('ui.admin') }}</a>@endif
                    <form class="d-inline" method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-wine btn-sm" type="submit">{{ __('ui.logout') }}</button></form>
                @else
                    <a class="btn btn-wine btn-sm" href="{{ route('login') }}">{{ __('ui.login') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if($notice)<div class="alert alert-warning rounded-0 mb-0 text-center">{{ $notice }}</div>@endif
@if(session('error'))<div class="container mt-3"><div class="alert alert-danger">{{ session('error') }}</div></div>@endif
@if(session('success'))<div class="container mt-3"><div class="alert alert-success">{{ session('success') }}</div></div>@endif
<main>@yield('content')</main>

<footer class="footer py-5"><div class="container"><div class="row"><div class="col-lg-7"><div class="h5">ПЕДСЛОВО</div><p class="mb-0 text-white-50">{{ $footerText }}</p></div><div class="col-lg-5 text-lg-end mt-3 mt-lg-0">@if($contactPhone)<div>{{ $contactPhone }}</div>@endif @if($contactEmail)<div>{{ $contactEmail }}</div>@endif</div></div></div></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    const body=document.body;
    const key='pedslovo-a11y';
    let state={enabled:false,size:20,contrast:false,images:true};
    try{state=Object.assign(state,JSON.parse(localStorage.getItem(key)||'{}'))}catch(e){}
    function apply(){
        body.classList.toggle('a11y',!!state.enabled);
        body.classList.toggle('a11y-high',!!state.enabled&&!!state.contrast);
        body.classList.toggle('a11y-hide-images',!!state.enabled&&!state.images);
        body.style.fontSize=state.enabled?state.size+'px':'';
        localStorage.setItem(key,JSON.stringify(state));
    }
    document.getElementById('a11yOn').addEventListener('click',()=>{state.enabled=true;apply()});
    document.getElementById('a11yOff').addEventListener('click',()=>{state.enabled=false;apply()});
    document.querySelectorAll('[data-a11y-size]').forEach(btn=>btn.addEventListener('click',()=>{state.size=Math.max(18,Math.min(30,state.size+(btn.dataset.a11ySize==='up'?2:-2)));apply()}));
    document.getElementById('a11yContrast').addEventListener('click',()=>{state.contrast=!state.contrast;apply()});
    document.getElementById('a11yImages').addEventListener('click',()=>{state.images=!state.images;apply()});
    apply();
})();
</script>
{!! $analyticsCode !!}
</body>
</html>
