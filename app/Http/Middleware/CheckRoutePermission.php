<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();
        $route = $request->route();

        if (!$user || !$route) {
            abort(403, 'Unauthorized');
        }

        $uri = $route->uri();
        $name = $route->getName();

        if (Str::startsWith($uri, '~admin') && $name) {
            if (Str::contains($uri, ['getData', 'login', 'logout', 'store', 'update'])) {
                return $next($request);
            }

            $permission = str_replace('.', ' ', $name);

            if (!$user->can($permission)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
