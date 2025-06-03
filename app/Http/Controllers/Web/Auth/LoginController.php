<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = new User();
    }
    public function index()
    {
        return view('auth.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|min:3|max:50',
            'password' => 'required|min:6|max:50',
        ]);

        DB::beginTransaction();

        try {
            $user = $this->user->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Email atau password salah.'], 401);
            }

            if ($user->email_verified_at == null) {
                return response()->json(['status' => false, 'message' => 'Akun anda belum diverifikasi. Silahkan hubungi admin.'], 401);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();
                DB::commit();

                $redirectUrl = match (true) {
                    $user->hasRole($this->user::SUPER_ADMIN) => url('/~admin'),
                    default => null,
                };

                if (!$redirectUrl) {
                    return response()->json(['status' => false, 'message' => 'Role tidak valid.'], 403);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil.',
                    'redirect' => $redirectUrl
                ]);
            }

            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Email atau password salah.'], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan. ' . $e->getMessage()], 500);
        }
    }
}
