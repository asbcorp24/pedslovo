<?php
namespace App\Http\Middleware;use Closure;
class RoleMiddleware{public function handle($request,Closure $next,...$roles){abort_unless(auth()->check()&&in_array(auth()->user()->role,$roles,true),403);return $next($request);}}
