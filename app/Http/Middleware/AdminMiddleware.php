<?php
namespace App\Http\Middleware;
use Closure;
class AdminMiddleware { public function handle($request, Closure $next){ abort_unless(auth()->check() && in_array(auth()->user()->role,['admin','editor']),403); return $next($request); } }
