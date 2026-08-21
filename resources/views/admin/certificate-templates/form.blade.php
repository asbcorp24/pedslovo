@extends('admin.layout')

@section('title',$template->exists ? 'Шаблон сертификата' : 'Новый шаблон сертификата')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><div class="small-label">Учебная часть</div><h1 class="mb-0">{{ $template->exists ? 'Шаблон сертификата' : 'Новый шаблон сертификата' }}</h1></div>
    <a href="{{ route('admin.certificate-templates.index') }}" class="btn btn-light">← К списку</a>
</div>

<form method="post" enctype="multipart/form-data" action="{{ $template->exists ? route('admin.certificate-templates.update',$template) : route('admin.certificate-templates.store') }}" id="certificateTemplateForm">
    @csrf
    @if($template->exists) @method('PUT') @endif

    <div class="card admin-card shadow-sm mb-4"><div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Название шаблона</label><input class="form-control" name="name" required value="{{ old('name',$template->name) }}"></div>
            <div class="col-md-4"><label class="form-label">Язык</label><select class="form-select" name="locale">@foreach(['ru'=>'Русский','cv'=>'Чувашский','mhr'=>'Марийский','tt'=>'Татарский'] as $k=>$v)<option value="{{ $k }}" @selected(old('locale',$template->locale ?: 'ru')===$k)>{{ $v }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Заголовок</label><input class="form-control" name="title" value="{{ old('title',$template->title ?: 'СЕРТИФИКАТ') }}"></div>
            <div class="col-12"><label class="form-label">Текст сертификата</label><textarea class="form-control" rows="4" name="body_text" placeholder="Настоящим подтверждается, что {student} успешно завершил(а) курс {course}">{{ old('body_text',$template->body_text) }}</textarea><div class="form-text">Переменные: <code>{student}</code>, <code>{course}</code>, <code>{score}</code>, <code>{number}</code>, <code>{date}</code>.</div></div>
            <div class="col-md-6"><label class="form-label">ФИО подписанта</label><input class="form-control" name="signer_name" value="{{ old('signer_name',$template->signer_name) }}"></div>
            <div class="col-md-6"><label class="form-label">Должность подписанта</label><input class="form-control" name="signer_position" value="{{ old('signer_position',$template->signer_position) }}"></div>
            <div class="col-md-4"><label class="form-label">Фон A4</label><input type="file" class="form-control" name="background" accept="image/*">@if($template->background_path)<div class="small mt-1">Загружен: {{ basename($template->background_path) }}</div>@endif</div>
            <div class="col-md-4"><label class="form-label">Подпись</label><input type="file" class="form-control" name="signature" accept="image/*">@if($template->signature_path)<div class="small mt-1">Загружена</div>@endif</div>
            <div class="col-md-4"><label class="form-label">Печать</label><input type="file" class="form-control" name="stamp" accept="image/*">@if($template->stamp_path)<div class="small mt-1">Загружена</div>@endif</div>
        </div>
        <div class="d-flex flex-wrap gap-4 mt-4">
            <div class="form-check"><input type="hidden" name="show_score" value="0"><input class="form-check-input" type="checkbox" name="show_score" value="1" @checked(old('show_score',$template->exists ? $template->show_score : true))><label class="form-check-label">Показывать итоговый балл</label></div>
            <div class="form-check"><input type="hidden" name="show_qr" value="0"><input class="form-check-input" type="checkbox" name="show_qr" value="1" @checked(old('show_qr',$template->exists ? $template->show_qr : true))><label class="form-check-label">Показывать QR проверки</label></div>
            <div class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$template->exists ? $template->is_active : true))><label class="form-check-label">Активен</label></div>
        </div>
    </div></div>

    <div class="card admin-card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Предпросмотр сертификата</h2>
                    <div class="text-muted small">Предпросмотр обновляется сразу при изменении полей. Используются демонстрационные данные.</div>
                </div>
                <span class="badge text-bg-warning">ДЕМО</span>
            </div>

            <div class="certificate-preview-wrap">
                <div id="certificatePreview" class="certificate-preview">
                    <div class="certificate-preview-content">
                        <div class="text-uppercase text-muted certificate-brand">Цифровой образовательный ресурс «Педслово»</div>
                        <div id="previewTitle" class="certificate-title">СЕРТИФИКАТ</div>
                        <div id="previewBody" class="certificate-body"></div>
                        <div id="previewDefaultBody" class="certificate-body">
                            Настоящим подтверждается, что<br>
                            <strong class="certificate-student">Иванов Иван Иванович</strong><br>
                            успешно завершил(а) курс<br>
                            <strong>«Музыкальная литература»</strong>
                        </div>
                        <div id="previewScore" class="mt-3">Итоговый балл: <strong>92</strong></div>

                        <div id="previewSignerBlock" class="row align-items-end mt-5 text-start">
                            <div class="col-7">
                                <div id="previewSignerPosition" class="small text-muted"></div>
                                <strong id="previewSignerName"></strong>
                                <div><img id="previewSignature" class="preview-signature d-none" alt="Подпись"></div>
                            </div>
                            <div class="col-5 text-end"><img id="previewStamp" class="preview-stamp d-none" alt="Печать"></div>
                        </div>

                        <div class="row mt-5 text-start align-items-end">
                            <div class="col">№ PED-20260821-DEMO1234<br><span class="small text-muted">21.08.2026</span></div>
                            <div id="previewQr" class="col text-end">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode(url('/certificate/verify/demo-preview')) }}" width="100" height="100" alt="Демо QR-код">
                                <div class="small text-muted">Проверка подлинности</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="small text-muted mt-2">Демо: Иванов Иван Иванович · «Музыкальная литература» · 92 балла · № PED-20260821-DEMO1234.</div>
        </div>
    </div>

    <button class="btn btn-primary btn-lg">Сохранить шаблон</button>
</form>

<style>
.certificate-preview-wrap{overflow:auto;background:#eee7df;border-radius:16px;padding:20px}.certificate-preview{width:100%;max-width:1000px;min-height:680px;margin:0 auto;background:#fff center/cover no-repeat;border:1px solid #d9d1ca;border-radius:16px;box-shadow:0 10px 30px rgba(45,28,30,.12);position:relative;overflow:hidden;text-align:center}.certificate-preview-content{position:relative;z-index:2;padding:48px;min-height:680px}.certificate-brand{font-size:.9rem}.certificate-title{font-size:2.7rem;font-weight:500;margin-top:1.5rem}.certificate-body{font-size:1.25rem;line-height:1.65;margin-top:2.5rem;white-space:pre-line}.certificate-student{font-size:1.55rem}.preview-signature{max-height:70px;max-width:220px}.preview-stamp{max-height:110px;max-width:150px}@media(max-width:767.98px){.certificate-preview{min-width:720px}.certificate-preview-content{padding:35px}}
</style>
@endsection

@push('scripts')
<script>
(function(){
    const form = document.getElementById('certificateTemplateForm');
    if (!form) return;

    const demo = {
        student: 'Иванов Иван Иванович',
        course: 'Музыкальная литература',
        score: '92',
        number: 'PED-20260821-DEMO1234',
        date: '21.08.2026'
    };

    const titleInput = form.querySelector('[name="title"]');
    const bodyInput = form.querySelector('[name="body_text"]');
    const signerNameInput = form.querySelector('[name="signer_name"]');
    const signerPositionInput = form.querySelector('[name="signer_position"]');
    const scoreInput = form.querySelector('[name="show_score"][type="checkbox"]');
    const qrInput = form.querySelector('[name="show_qr"][type="checkbox"]');
    const preview = document.getElementById('certificatePreview');
    const previewTitle = document.getElementById('previewTitle');
    const previewBody = document.getElementById('previewBody');
    const defaultBody = document.getElementById('previewDefaultBody');
    const previewScore = document.getElementById('previewScore');
    const previewQr = document.getElementById('previewQr');
    const signerBlock = document.getElementById('previewSignerBlock');
    const signerName = document.getElementById('previewSignerName');
    const signerPosition = document.getElementById('previewSignerPosition');
    const signature = document.getElementById('previewSignature');
    const stamp = document.getElementById('previewStamp');

    let signaturePresent = {{ $template->signature_path ? 'true' : 'false' }};
    let stampPresent = {{ $template->stamp_path ? 'true' : 'false' }};

    function renderText(text) {
        return (text || '')
            .replaceAll('{student}', demo.student)
            .replaceAll('{course}', demo.course)
            .replaceAll('{score}', demo.score)
            .replaceAll('{number}', demo.number)
            .replaceAll('{date}', demo.date);
    }

    function updatePreview() {
        previewTitle.textContent = titleInput.value.trim() || 'СЕРТИФИКАТ';
        const body = bodyInput.value.trim();
        previewBody.textContent = body ? renderText(body) : '';
        previewBody.classList.toggle('d-none', !body);
        defaultBody.classList.toggle('d-none', !!body);
        previewScore.classList.toggle('d-none', !scoreInput.checked);
        previewQr.classList.toggle('d-none', !qrInput.checked);
        signerName.textContent = signerNameInput.value.trim();
        signerPosition.textContent = signerPositionInput.value.trim();
        const hasSigner = !!signerNameInput.value.trim() || !!signerPositionInput.value.trim() || signaturePresent || stampPresent;
        signerBlock.classList.toggle('d-none', !hasSigner);
    }

    function bindImage(inputName, img, kind) {
        const input = form.querySelector('[name="'+inputName+'"]');
        input.addEventListener('change', function(){
            const file = input.files && input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e){
                if (kind === 'background') {
                    preview.style.backgroundImage = 'url("'+e.target.result+'")';
                } else {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                    if (kind === 'signature') signaturePresent = true;
                    if (kind === 'stamp') stampPresent = true;
                    updatePreview();
                }
            };
            reader.readAsDataURL(file);
        });
    }

    @if($template->background_path)
        preview.style.backgroundImage = 'url("{{ asset('storage/'.$template->background_path) }}")';
    @endif
    @if($template->signature_path)
        signature.src = @json(asset('storage/'.$template->signature_path));
        signature.classList.remove('d-none');
    @endif
    @if($template->stamp_path)
        stamp.src = @json(asset('storage/'.$template->stamp_path));
        stamp.classList.remove('d-none');
    @endif

    [titleInput, bodyInput, signerNameInput, signerPositionInput].forEach(function(el){
        el.addEventListener('input', updatePreview);
    });
    [scoreInput, qrInput].forEach(function(el){
        el.addEventListener('change', updatePreview);
    });

    bindImage('background', null, 'background');
    bindImage('signature', signature, 'signature');
    bindImage('stamp', stamp, 'stamp');
    updatePreview();
})();
</script>
@endpush
