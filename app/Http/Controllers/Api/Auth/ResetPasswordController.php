<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:3|max:50',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), $validator->errors(), isList: true);
        }

        try {
            $data = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            DB::commit();

            if ($data == Password::PASSWORD_RESET) {
                return Message::success('Successfully reset password');
            } else {
                return Message::warning('Failed to reset password');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error($th->getMessage());
        }
    }
}
