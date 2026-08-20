<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Админ — Педслово')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f4f1ed}.sidebar{background:linear-gradient(180deg,#551728,#32101b);min-height:100vh;position:sticky;top:0}.sidebar a{display:flex;align-items:center;gap:.55rem;color:#f5e8dc;text-decoration:none;padding:.58rem .7rem;border-radius:.6rem}.sidebar a:hover{background:rgba(255,255,255,.1);color:#fff}.admin-card{border:0;border-radius:18px}.drag-handle{cursor:grab;color:#999;font-size:1.2rem}.dragging{opacity:.45}.small-label{text-transform:uppercase;letter-spacing:.08em;font-size:.7rem;color:#8a6a59}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-xl-2 sidebar p-3 p-lg-4">
            <a href="{{ route('home') }}" class="h5 text-white mb-2">♪ ПЕДСЛОВО</a>
            <div class="small text-white-50 px-2 mb-3">Учебная часть</div>
            <a href="{{ route('admin.dashboard') }}">Обзор</a>
            <a href="{{ route('admin.sections.index') }}">Разделы и специальности</a>
            <a href="{{ route('admin.materials.index') }}">Материалы</a>
            <a href="{{ route('admin.courses.index') }}">Учебные курсы</a>
            <a href="{{ route('admin.scorm.index') }}">SCORM / iSpring</a>
            <a href="{{ route('admin.media.index') }}">Медиатека</a>
            <a href="{{ route('admin.seo.index') }}">SEO</a>

            @if(auth()->user()->role === 'admin')
                <hr class="border-light opacity-25">
                <div class="small text-white-50 px-2">Учебная часть</div>
                <a href="{{ route('admin.groups.index') }}">Учебные группы</a>
                <a href="{{ route('admin.users.index') }}">Пользователи</a>
                <a href="{{ route('admin.journal.index') }}">Журнал</a>
                <a href="{{ route('admin.scorm-results.index') }}">Результаты SCORM</a>
                <a href="{{ route('admin.settings.edit') }}">Настройки сайта</a>
            @endif

            <hr class="border-light opacity-25">
            <a href="{{ route('cabinet') }}">Личный кабинет</a>
        </aside>

        <main class="col-md-9 col-xl-10 p-3 p-lg-5">
            @if(session('ok'))
                <div class="alert alert-success">{{ session('ok') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Проверьте данные:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script>
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
