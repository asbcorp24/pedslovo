<?php
namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', config('app.locale','ru'));
        if (!in_array($locale,['ru','cv','mhr','tt'],true)) {
            $locale = 'ru';
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
