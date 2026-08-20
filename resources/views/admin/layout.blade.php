<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Админ — Педслово')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f4f1ed;color:#2a2022}.sidebar{background:linear-gradient(180deg,#551728,#32101b);min-height:100vh;position:sticky;top:0}.sidebar-brand{display:flex;align-items:center;gap:.65rem;color:#fff;text-decoration:none;font-weight:800;font-size:1.15rem;padding:.4rem .55rem}.sidebar-section{color:rgba(255,255,255,.5);font-size:.68rem;text-transform:uppercase;letter-spacing:.12em;padding:.9rem .75rem .35rem}.sidebar a.nav-link{display:flex;align-items:center;gap:.7rem;color:#f5e8dc;text-decoration:none;padding:.62rem .75rem;border-radius:.7rem;margin-bottom:.15rem}.sidebar a.nav-link:hover,.sidebar a.nav-link.active{background:rgba(255,255,255,.12);color:#fff}.sidebar a.nav-link.active{box-shadow:inset 3px 0 0 #d7b35b}.admin-card{border:0;border-radius:18px}.drag-handle{cursor:grab;color:#999;font-size:1.2rem}.dragging{opacity:.45}.small-label{text-transform:uppercase;letter-spacing:.08em;font-size:.7rem;color:#8a6a59}.admin-topbar{display:none}.menu-icon{width:1.2rem;text-align:center;opacity:.9}.sidebar-footer{margin-top:auto}.sidebar-wrap{display:flex;flex-direction:column;min-height:calc(100vh - 2rem)}
        @media(max-width:767.98px){.admin-topbar{display:flex;position:sticky;top:0;z-index:1030;background:#fff;border-bottom:1px solid #e7dfd8;padding:.65rem .9rem;align-items:center;justify-content:space-between}.sidebar{position:fixed;left:-290px;top:0;width:280px;z-index:1040;transition:.22s;box-shadow:0 0 30px rgba(0,0,0,.2)}.sidebar.open{left:0}.admin-main{width:100%!important;padding:1rem!important}.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1035}.sidebar-overlay.show{display:block}}
    </style>
</head>
<body>
@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $active = function ($patterns) {
        foreach ((array)$patterns as $pattern) {
            if (request()->routeIs($pattern)) return 'active';
        }
        return '';
    };
@endphp

<div class="admin-topbar">
    <button class="btn btn-outline-dark btn-sm" type="button" id="menuToggle">☰ Меню</button>
    <strong>Педслово</strong>
    <a href="{{ route('cabinet') }}" class="btn btn-light btn-sm">Кабинет</a>
</div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-xl-2 sidebar p-3 p-lg-4" id="adminSidebar">
            <div class="sidebar-wrap">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand"><span>♪</span><span>ПЕДСЛОВО</span></a>
                <div class="small text-white-50 px-2 mb-2">Панель управления</div>

                <div class="sidebar-section">Главное</div>
                <a class="nav-link {{ $active('admin.dashboard') }}" href="{{ route('admin.dashboard') }}"><span class="menu-icon">⌂</span>Обзор</a>

                <div class="sidebar-section">Учебный контент</div>
                <a class="nav-link {{ $active('admin.sections.*') }}" href="{{ route('admin.sections.index') }}"><span class="menu-icon">▦</span>Разделы и специальности</a>
                <a class="nav-link {{ $active('admin.courses.*') }}" href="{{ route('admin.courses.index') }}"><span class="menu-icon">▤</span>Курсы и уроки</a>
                <a class="nav-link {{ $active('admin.materials.*') }}" href="{{ route('admin.materials.index') }}"><span class="menu-icon">□</span>Материалы</a>
                <a class="nav-link {{ $active('admin.media.*') }}" href="{{ route('admin.media.index') }}"><span class="menu-icon">▶</span>Медиатека</a>
                <a class="nav-link {{ $active('admin.scorm.*') }}" href="{{ route('admin.scorm.index') }}"><span class="menu-icon">✓</span>SCORM / iSpring</a>

                @if($isAdmin)
                    <div class="sidebar-section">Учебная часть</div>
                    <a class="nav-link {{ $active('admin.groups.*') }}" href="{{ route('admin.groups.index') }}"><span class="menu-icon">👥</span>Учебные группы</a>
                    <a class="nav-link {{ $active('admin.users.*') }}" href="{{ route('admin.users.index') }}"><span class="menu-icon">♙</span>Пользователи</a>
                    @if(Route::has('admin.student-passwords.index'))
                        <a class="nav-link {{ $active('admin.student-passwords.*') }}" href="{{ route('admin.student-passwords.index') }}"><span class="menu-icon">🔑</span>Пароли студентов</a>
                    @endif
                    <a class="nav-link {{ $active('admin.journal.*') }}" href="{{ route('admin.journal.index') }}"><span class="menu-icon">▣</span>Журнал</a>
                    <a class="nav-link {{ $active('admin.scorm-results.*') }}" href="{{ route('admin.scorm-results.index') }}"><span class="menu-icon">%</span>Результаты SCORM</a>
                @endif

                <div class="sidebar-section">Сайт</div>
                <a class="nav-link {{ $active('admin.seo.*') }}" href="{{ route('admin.seo.index') }}"><span class="menu-icon">⌕</span>SEO</a>
                @if($isAdmin)
                    <a class="nav-link {{ $active('admin.settings.*') }}" href="{{ route('admin.settings.edit') }}"><span class="menu-icon">⚙</span>Настройки сайта</a>
                @endif

                <div class="sidebar-footer mt-auto pt-4">
                    <hr class="border-light opacity-25">
                    <a class="nav-link" href="{{ route('help') }}"><span class="menu-icon">?</span>Справка</a>
                    <a class="nav-link" href="{{ route('home') }}"><span class="menu-icon">↗</span>Открыть сайт</a>
                    <a class="nav-link" href="{{ route('cabinet') }}"><span class="menu-icon">●</span>Личный кабинет</a>
                    <form method="post" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button class="btn btn-outline-light btn-sm w-100" type="submit">Выйти</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="col-md-9 col-xl-10 p-3 p-lg-5 admin-main">
            @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-danger"><strong>Проверьте данные:</strong><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar=document.getElementById('adminSidebar');
const overlay=document.getElementById('sidebarOverlay');
const toggle=document.getElementById('menuToggle');
function closeMenu(){sidebar?.classList.remove('open');overlay?.classList.remove('show')}
toggle?.addEventListener('click',()=>{sidebar?.classList.toggle('open');overlay?.classList.toggle('show')});
overlay?.addEventListener('click',closeMenu);

document.querySelectorAll('.js-sortable').forEach(box=>{
    let row=null;
    box.querySelectorAll('[data-sort-id]').forEach(r=>{
        r.draggable=true;
        r.addEventListener('dragstart',()=>{row=r;r.classList.add('dragging')});
        r.addEventListener('dragend',async()=>{
            r.classList.remove('dragging');
            const ids=[...box.querySelectorAll('[data-sort-id]')].map(x=>Number(x.dataset.sortId));
            await fetch(box.dataset.sortUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({ids})});
            row=null;
        });
        r.addEventListener('dragover',e=>{
            e.preventDefault();
            if(!row||row===r)return;
            const rect=r.getBoundingClientRect();
            r.parentNode.insertBefore(row,e.clientY<rect.top+rect.height/2?r:r.nextSibling);
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
