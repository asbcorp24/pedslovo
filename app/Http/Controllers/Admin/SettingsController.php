<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private array $locales = ['ru','cv','mhr','tt'];
    private array $homeFields = ['home_badge','home_title','home_subtitle','home_about_title','home_about_text'];

    public function edit()
    {
        $settings = SiteSetting::pluck('value','key');

        // Совместимость со старыми одязычными настройками: показываем их как русский текст.
        foreach ($this->homeFields as $field) {
            $ruKey = $field.'_ru';
            if (!isset($settings[$ruKey]) && isset($settings[$field])) {
                $settings[$ruKey] = $settings[$field];
            }
        }

        return view('admin.settings.index', [
            'settings' => $settings,
            'locales' => [
                'ru' => 'Русский',
                'cv' => 'Чувашский',
                'mhr' => 'Марийский',
                'tt' => 'Татарский',
            ],
        ]);
    }

    public function update(Request $r)
    {
        $rules = [
            'college_name'=>'nullable|max:255',
            'college_short_name'=>'nullable|max:255',
            'college_site'=>'nullable|max:2048',
            'contact_phone'=>'nullable|max:100',
            'contact_email'=>'nullable|email|max:255',
            'address'=>'nullable|max:500',
            'footer_text'=>'nullable|max:1000',
            'analytics_code'=>'nullable|max:10000',
            'maintenance_notice'=>'nullable|max:2000',
        ];

        foreach ($this->locales as $locale) {
            $rules['home_badge_'.$locale] = 'nullable|max:255';
            $rules['home_title_'.$locale] = 'nullable|max:500';
            $rules['home_subtitle_'.$locale] = 'nullable|max:1500';
            $rules['home_about_title_'.$locale] = 'nullable|max:255';
            $rules['home_about_text_'.$locale] = 'nullable|max:5000';
        }

        $data = $r->validate($rules);

        foreach ($data as $key => $value) {
            $group = str_starts_with($key, 'home_') ? 'home' : 'site';
            SiteSetting::put($key, $value, $group);
        }

        // Оставляем старые ключи синхронизированными с русской версией для обратной совместимости.
        foreach ($this->homeFields as $field) {
            $ruKey = $field.'_ru';
            if (array_key_exists($ruKey, $data)) {
                SiteSetting::put($field, $data[$ruKey], 'home');
            }
        }

        return back()->with('success','Настройки сохранены');
    }
}
