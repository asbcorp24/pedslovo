<?php

namespace App\Http\Controllers;

use App\Models\Section;

class SectionController extends Controller
{
    public function show(Section $section)
    {
        abort_unless($section->is_active, 404);

        $section->load([
            'translations',
            'parent.translations',
            'children' => fn($q) => $q->where('is_active', true)->with('translations'),
            'materials' => fn($q) => $q->published()->latest('published_at'),
            'courses' => fn($q) => $q->where('is_active', true)->with(['instructor','section.translations'])->orderBy('study_year')->orderBy('sort_order'),
        ]);

        if ($section->type === 'specialty') {
            return view('specialty', compact('section'));
        }

        return view('section', compact('section'));
    }
}
