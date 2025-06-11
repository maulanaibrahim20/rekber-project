<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestResponseLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            $userId = optional($request->user())->id;

            // Log request ke channel 'api'
            Log::channel('api')->info("[API REQUEST]", [
                'method'   => $request->method(),
                'url'      => $request->fullUrl(),
                'ip'       => $request->ip(),
                'user_id'  => $userId,
                'input'    => $request->except(['password', 'password_confirmation']),
            ]);

            // Ambil isi response
            $responseContent = null;
            if (method_exists($response, 'getContent')) {
                $responseContent = $response->getContent();

                // Batasi ukuran
                if (strlen($responseContent) > 2000) {
                    $responseContent = substr($responseContent, 0, 2000) . '... [truncated]';
                }

                $decoded = json_decode($responseContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $responseContent = $decoded;
                }
            }

            // Log response ke channel 'api'
            Log::channel('api')->info("[API RESPONSE]", [
                'url'     => $request->fullUrl(),
                'status'  => $response->status(),
                'response' => $responseContent,
            ]);
        }

        return $response;
    }
}
