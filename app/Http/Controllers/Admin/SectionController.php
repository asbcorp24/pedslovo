<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('parent')->orderBy('sort_order')->orderBy('title')->get();
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.sections.form', [
            'section' => new Section,
            'parents' => Section::orderBy('title')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);
        Section::create($data);
        return redirect()->route('admin.sections.index')->with('ok', 'Раздел создан');
    }

    public function edit(Section $section)
    {
        return view('admin.sections.form', [
            'section' => $section,
            'parents' => Section::whereKeyNot($section->id)->orderBy('title')->get(),
        ]);
    }

    public function update(Request $r, Section $section)
    {
        $section->update($this->validated($r, $section->id));
        return redirect()->route('admin.sections.index')->with('ok', 'Раздел обновлён');
    }

    public function destroy(Section $section)
    {
        abort_if($section->children()->exists(), 422, 'Сначала удалите дочерние разделы');
        $section->delete();
        return back()->with('ok', 'Раздел удалён');
    }

    private function validated(Request $r, $id = null)
    {
        $data = $r->validate([
            'parent_id' => 'nullable|exists:sections,id',
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'type' => 'nullable|max:50',
            'description' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = $slug !== '' ? Str::slug($slug) : Str::slug($data['title']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $r->boolean('is_active');

        return $data;
    }
}
