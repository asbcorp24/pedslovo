<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        return view('admin.certificate-templates.index', [
            'templates' => CertificateTemplate::withCount('courses')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.certificate-templates.form', ['template' => new CertificateTemplate]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $data = $this->uploads($request, $data);
        $template = CertificateTemplate::create($data);
        return redirect()->route('admin.certificate-templates.edit', $template)->with('success','Шаблон сертификата создан');
    }

    public function edit(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.form', ['template' => $certificateTemplate]);
    }

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $data = $this->data($request);
        $data = $this->uploads($request, $data, $certificateTemplate);
        $certificateTemplate->update($data);
        return back()->with('success','Шаблон сертификата обновлён');
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        abort_if($certificateTemplate->courses()->exists(), 422, 'Шаблон используется в курсах');
        foreach (['background_path','signature_path','stamp_path'] as $field) {
            if ($certificateTemplate->{$field}) Storage::disk('public')->delete($certificateTemplate->{$field});
        }
        $certificateTemplate->delete();
        return back()->with('success','Шаблон удалён');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name'=>'required|max:255',
            'locale'=>'required|in:ru,cv,mhr,tt',
            'title'=>'nullable|max:1000',
            'body_text'=>'nullable|max:5000',
            'signer_name'=>'nullable|max:255',
            'signer_position'=>'nullable|max:255',
            'background'=>'nullable|image|max:10240',
            'signature'=>'nullable|image|max:5120',
            'stamp'=>'nullable|image|max:5120',
            'show_score'=>'nullable|boolean',
            'show_qr'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
        ]);
        unset($data['background'],$data['signature'],$data['stamp']);
        $data['show_score'] = $request->boolean('show_score');
        $data['show_qr'] = $request->boolean('show_qr');
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function uploads(Request $request, array $data, ?CertificateTemplate $template = null): array
    {
        foreach (['background'=>'background_path','signature'=>'signature_path','stamp'=>'stamp_path'] as $input=>$field) {
            if (!$request->hasFile($input)) continue;
            if ($template && $template->{$field}) Storage::disk('public')->delete($template->{$field});
            $data[$field] = $request->file($input)->store('certificates', 'public');
        }
        return $data;
    }
}
