<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $admin;
    public function __construct()
    {
        $this->admin = new Admin();
    }
    public function index()
    {
        return view('auth.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|min:3|max:50',
            'password' => 'required|min:6|max:50',
        ]);

        DB::beginTransaction();

        try {
            $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $admin = $this->admin->where($loginField, $request->login)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json(['status' => false, 'message' => 'Email/Username atau password salah.'], 401);
            }

            if ($admin->email_verified_at === null) {
                return response()->json(['status' => false, 'message' => 'Akun anda belum diverifikasi. Silahkan hubungi admin.'], 401);
            }

            if (Auth::guard('admin')->attempt([$loginField => $request->login, 'password' => $request->password])) {
                $request->session()->regenerate();
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil.',
                    'redirect' => url('/~admin')
                ]);
            }

            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Email/Username atau password salah.'], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan. ' . $e->getMessage()], 500);
        }
    }
}
