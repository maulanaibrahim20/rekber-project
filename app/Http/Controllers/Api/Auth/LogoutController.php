<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $data = $request->user()->currentAccessToken()->delete();

            return Message::success('Logout successfully', $data);
        } catch (\Throwable $th) {
            return Message::error($th->getMessage());
        }
    }
}
