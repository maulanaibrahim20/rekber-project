<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken() ?? $request->cookie('auth_token');

            if ($token) {
                $tokenData = PersonalAccessToken::findToken($token);
                if ($tokenData) {
                    $tokenData->delete();
                }
            }

            $response = Message::success('Logout successfully');

            return $response->withCookie(
                Cookie::forget('auth_token')
            );
        } catch (\Throwable $e) {
            return Message::error('Logout failed: ' . $e->getMessage());
        }
    }
}
