<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:50',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), $validator->errors(), isList: true);
        }

        $user = User::whereEmail($request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Message::warning("Please check your email and password");
        }

        if (!$user->hasVerifiedEmail()) {
            return Message::unauhtorize('Email not verified');
        }

        try {
            $token = $user->createToken('api', ['user'])->plainTextToken;

            DB::commit();

            return Message::success('Login successfully', [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error($th->getMessage());
        }
    }

    public function checkAuth(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return Message::unauhtorize();
            }

            $tokenData = PersonalAccessToken::findToken($token);

            if (!$tokenData) {
                return Message::unauhtorize();
            }

            $user = $tokenData->tokenable;

            if (!$user) {
                return Message::unauhtorize();
            }

            $tokenData->delete();

            return Message::success('Token is valid', [
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return Message::error('Authentication check failed' . $e->getMessage());
        }
    }
}
