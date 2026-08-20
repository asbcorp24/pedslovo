<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    private const LOCALES = ['ru','cv','mhr','tt'];

    public function index()
    {
        $sections = Section::with(['parent','translations'])->orderBy('sort_order')->orderBy('title')->get();
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.sections.form', [
            'section' => new Section,
            'parents' => Section::with('translations')->orderBy('title')->get(),
            'locales' => self::LOCALES,
            'availableCourses' => collect(),
        ]);
    }

    public function store(Request $r)
    {
        [$data, $translations] = $this->validated($r);

        DB::transaction(function () use ($data, $translations) {
            $section = Section::create($data);
            $this->syncTranslations($section, $translations);
        });

        return redirect()->route('admin.sections.index')->with('ok', 'Раздел создан');
    }

    public function edit(Section $section)
    {
        $section->load([
            'translations',
            'courses.lessons',
            'children.translations',
            'children.courses.lessons',
        ]);

        $sectionIds = collect([$section->id])->merge($section->children->pluck('id'))->all();
        $availableCourses = Course::with('section')
            ->where(function ($q) use ($sectionIds) {
                $q->whereNull('section_id')->orWhereNotIn('section_id', $sectionIds);
            })
            ->orderBy('title')
            ->get();

        return view('admin.sections.form', [
            'section' => $section,
            'parents' => Section::with('translations')->whereKeyNot($section->id)->orderBy('title')->get(),
            'locales' => self::LOCALES,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function update(Request $r, Section $section)
    {
        [$data, $translations] = $this->validated($r, $section->id);

        DB::transaction(function () use ($section, $data, $translations) {
            $section->update($data);
            $this->syncTranslations($section, $translations);
        });

        return redirect()->route('admin.sections.index')->with('ok', 'Раздел обновлён');
    }

    public function assignCourse(Request $request, Section $section)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($data['course_id']);
        $course->update(['section_id' => $section->id]);

        return back()->with('ok', 'Готовый курс «'.$course->title.'» привязан к разделу «'.$section->localizedTitle('ru').'».');
    }

    public function destroy(Section $section)
    {
        abort_if($section->children()->exists(), 422, 'Сначала удалите дочерние разделы');
        $section->delete();
        return back()->with('ok', 'Раздел удалён');
    }

    private function validated(Request $r, $id = null): array
    {
        $rules = [
            'parent_id' => 'nullable|exists:sections,id',
            'slug' => 'nullable|max:255',
            'type' => 'nullable|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'title_ru' => 'required|max:255',
            'description_ru' => 'nullable',
        ];

        foreach (['cv','mhr','tt'] as $locale) {
            $rules['title_'.$locale] = 'nullable|max:255';
            $rules['description_'.$locale] = 'nullable';
        }

        $validated = $r->validate($rules);
        $titleRu = trim((string) $validated['title_ru']);
        $slug = trim((string) ($validated['slug'] ?? ''));

        $data = [
            'parent_id' => $validated['parent_id'] ?? null,
            'title' => $titleRu,
            'description' => $validated['description_ru'] ?? null,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($titleRu),
            'type' => $validated['type'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $r->boolean('is_active'),
        ];

        $translations = [];
        foreach (self::LOCALES as $locale) {
            $translations[$locale] = [
                'title' => trim((string) ($validated['title_'.$locale] ?? '')),
                'description' => $validated['description_'.$locale] ?? null,
            ];
        }

        return [$data, $translations];
    }

    private function syncTranslations(Section $section, array $translations): void
    {
        foreach ($translations as $locale => $translation) {
            if ($locale !== 'ru' && $translation['title'] === '' && trim((string) $translation['description']) === '') {
                $section->translations()->where('locale', $locale)->delete();
                continue;
            }

            $section->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translation['title'] !== '' ? $translation['title'] : $section->title,
                    'description' => $translation['description'],
                ]
            );
        }
    }
}
