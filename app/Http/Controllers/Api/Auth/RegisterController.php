<?php

namespace App\Http\Controllers\Api\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = new User();
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|min:5|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]
        );

        if ($validate->fails()) {
            return Message::validator(
                $validate->errors()->first(),
                $validate->errors()
            );
        }

        try {
            $user = $this->user->create([
                'name' => $request->name,
                'username' => $request->username,
                'linkname' => Str::slug($request->username),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->sendEmailVerificationNotification();

            DB::commit();

            return Message::success('Registration successful', $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error($th->getMessage());
        }
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = $this->user->findOrFail($id);

        if (! hash_equals(sha1($user->email), $hash)) {
            return Message::forbidden('Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return Message::warning('Email sudah diverifikasi.');
        }

        $user->markEmailAsVerified();

        return Message::success('Email berhasil diverifikasi.', [
            'user' => $user,
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Message::warning('User tidak ditemukan.');
        }

        if ($user->hasVerifiedEmail()) {
            return Message::warning('Email sudah diverifikasi.');
        }

        $user->notify(new VerifyEmail());

        return Message::success('Email verifikasi berhasil dikirim.', [
            'user' => $user,
        ]);
    }
}
