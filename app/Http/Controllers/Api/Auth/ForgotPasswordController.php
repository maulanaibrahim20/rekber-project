<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }
    public function forgotPassword(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:3|max:50',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), $validator->errors(), isList: true);
        }

        $email = $this->user->where('email', $request->email)->first();

        if (!$email) {
            return Message::warning('Email not found');
        }

        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status == Password::RESET_LINK_SENT) {
                DB::commit();
                return Message::success('Success reset link sent to your email');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error($th->getMessage());
        }
    }
}
