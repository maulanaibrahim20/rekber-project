<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcceptJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
            return $next($request);
        }

        return response()->json([
            'status' => [
                'key' => 'invalid_accept_header',
                'value' => 'Header Accept: application/json is required.'
            ]
        ], 406);
    }
}
