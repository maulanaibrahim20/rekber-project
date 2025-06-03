<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index(Request $request)
    {
        $user = $this->user->where('id', Auth::user()->id)->first();

        return Message::success('User retrieved successfully', $user);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|string|max:255',
            'username'         => 'nullable|string|max:255|unique:users,username,' . Auth::id(),
            'linkname'         => 'nullable|string|max:255|unique:users,linkname,' . Auth::id(),
            'email'            => 'nullable|string|email|max:255|unique:users,email,' . Auth::id(),
            'password'         => 'nullable|string|min:8|confirmed',
            'phone'            => 'nullable|string|max:20',
            'birth_date'       => 'nullable|date',
            'address'          => 'nullable|string|max:255',
            'bio'              => 'nullable|string|max:255',
            'gender'           => 'nullable|in:male,female',
            'is_private'       => 'nullable|boolean',
            'profile_picture'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), $validator->errors(), isList: true);
        }
        $userId = Auth::id();

        $user = User::find($userId);

        try {
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::exists($user->profile_picture)) {
                    Storage::delete($user->profile_picture);
                }

                $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $user->update([
                'name'           => $request->name ?? $user->name,
                'username'       => $request->username ?? $user->username,
                'linkname'       => Str::slug($request->username) ?? $user->linkname,
                'email'          => $request->email ?? $user->email,
                'phone'          => $request->phone ?? $user->phone,
                'birth_date'     => $request->birth_date ?? $user->birth_date,
                'address'        => $request->address ?? $user->address,
                'bio'            => $request->bio ?? $user->bio,
                'gender'         => $request->gender ?? $user->gender,
                'is_private'     => $request->has('is_private') ? $request->is_private : $user->is_private,
                'password'       => $request->filled('password') ? Hash::make($request->password) : $user->password,
                'profile_picture' => $profilePicture ?? $user->profile_picture,
            ]);

            DB::commit();
            return Message::success('User updated successfully', $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while updating the user: ' . $th->getMessage());
        }
    }
}
