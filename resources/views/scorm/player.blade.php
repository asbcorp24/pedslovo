<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $scorm->title }}</title>
    <style>
        html,body{margin:0;height:100%;background:#111;font-family:Arial,sans-serif}
        .bar{height:48px;background:#fff;display:flex;align-items:center;gap:16px;padding:0 16px;border-bottom:1px solid #ddd}
        .bar a{text-decoration:none;color:#222}
        .title{font-weight:700;flex:1}
        .frame{width:100%;height:calc(100% - 49px);border:0;background:#fff}
    </style>
</head>
<body>
@php
    $returnUrl = $attempt->lesson_id ? route('learning.lesson',$attempt->lesson_id) : route('home');
@endphp
<div class="bar">
    <a href="{{ $returnUrl }}">← {{ __('Назад к уроку') }}</a>
    <div class="title">{{ $scorm->title }}</div>
    <span>SCORM {{ $scorm->version }}</span>
</div>
<iframe id="scormFrame" class="frame" src="{{ asset('storage/'.$scorm->storage_path.'/'.$scorm->launch_path) }}" allowfullscreen></iframe>
<script>
const endpoint = @json(route('scorm.commit',$attempt));
const stateEndpoint = @json(route('scorm.state',$attempt));
const token = @json(csrf_token());
const returnUrl = @json($returnUrl);
const store = {};
let initialized = false;
let leaving = false;

function commit() {
    return fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({data: store}),
        keepalive: true
    }).then(function(){ return true; }).catch(function(){ return false; });
}

function finishAndReturn() {
    if (leaving) return;
    leaving = true;
    initialized = false;

    let redirected = false;
    const goBack = function() {
        if (redirected) return;
        redirected = true;
        window.location.href = returnUrl;
    };

    commit().then(goBack).catch(goBack);
    setTimeout(goBack, 1200);
}

function getValue(key) {
    return Object.prototype.hasOwnProperty.call(store,key) ? String(store[key]) : '';
}

function setValue(key,value) {
    store[key] = String(value);
    return 'true';
}

window.API = {
    LMSInitialize: function(){ initialized = true; return 'true'; },
    LMSFinish: function(){ finishAndReturn(); return 'true'; },
    LMSGetValue: function(key){ return getValue(key); },
    LMSSetValue: function(key,value){ return setValue(key,value); },
    LMSCommit: function(){ commit(); return 'true'; },
    LMSGetLastError: function(){ return '0'; },
    LMSGetErrorString: function(){ return 'No error'; },
    LMSGetDiagnostic: function(){ return ''; }
};

window.API_1484_11 = {
    Initialize: function(){ initialized = true; return 'true'; },
    Terminate: function(){ finishAndReturn(); return 'true'; },
    GetValue: function(key){ return getValue(key); },
    SetValue: function(key,value){ return setValue(key,value); },
    Commit: function(){ commit(); return 'true'; },
    GetLastError: function(){ return '0'; },
    GetErrorString: function(){ return 'No error'; },
    GetDiagnostic: function(){ return ''; }
};

fetch(stateEndpoint,{headers:{'Accept':'application/json'}})
    .then(function(response){ return response.json(); })
    .then(function(data){ if(data.data) Object.assign(store,data.data); })
    .catch(function(){});

window.addEventListener('beforeunload',function(){ if(!leaving) commit(); });
setInterval(function(){ if(initialized && !leaving) commit(); },30000);
</script>
</body>
</html>
