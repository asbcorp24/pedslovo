<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\Section;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::roots()
            ->where('is_active', true)
            ->with(['children' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $studentRoot = $sections->firstWhere('title', 'Студентам и абитуриентам');
        $specialties = $studentRoot
            ? $studentRoot->children()->where('type', 'specialty')->where('is_active', true)->withCount('courses')->get()
            : collect();

        $latest = Material::published()->latest('published_at')->limit(6)->get();
        $featuredCourses = Course::where('is_active', true)
            ->with('section')
            ->orderBy('study_year')
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $settings = SiteSetting::where('group', 'home')->pluck('value', 'key');
        $locale = app()->getLocale();
        $home = [];

        foreach (['home_badge','home_title','home_subtitle','home_about_title','home_about_text'] as $field) {
            $localizedKey = $field.'_'.$locale;
            $ruKey = $field.'_ru';

            // Приоритет: выбранный язык -> русский вариант -> старый одязычный ключ.
            $home[$field] = $settings[$localizedKey]
                ?? $settings[$ruKey]
                ?? $settings[$field]
                ?? null;
        }

        return view('home', compact('sections','specialties','latest','featuredCourses','home'));
    }
}
