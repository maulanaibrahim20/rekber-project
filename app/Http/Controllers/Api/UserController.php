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

        return Message::success('User retrieved successfully', new UserResource($user));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|string|max:255',
            'username'         => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'unique:users,username',
                'regex:/^(?!.*\.\.)(?!.*\.$)(?!^\.)[a-zA-Z0-9._]+$/',
            ],
            'linkname'         => 'nullable|string|max:255|unique:users,linkname,' . Auth::id(),
            'email'            => 'nullable|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone'            => 'nullable|string|max:20',
            'birth_date'       => 'nullable|date',
            'address'          => 'nullable|string|max:255',
            'bio'              => 'nullable|string|max:255',
            'gender'           => 'nullable|in:male,female',
            'is_private'       => 'nullable|boolean',
            'profile_picture'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        $user = Auth::user();

        try {
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::exists($user->profile_picture)) {
                    Storage::delete($user->profile_picture);
                }

                $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $user->update([
                'name'            => $request->name ?? $user->name,
                'username'        => $request->username ?? $user->username,
                'linkname'        => Str::slug($request->username) ?? $user->linkname,
                'email'           => $request->email ?? $user->email,
                'phone'           => $request->phone ?? $user->phone,
                'birth_date'      => $request->birth_date ?? $user->birth_date,
                'address'         => $request->address ?? $user->address,
                'bio'             => $request->bio ?? $user->bio,
                'gender'          => $request->gender ?? $user->gender,
                'is_private'      => $request->has('is_private') ? $request->is_private : $user->is_private,
                'profile_picture' => $profilePicture ?? $user->profile_picture,
            ]);

            DB::commit();
            return Message::success('User updated successfully', new UserResource($user->fresh()));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred while updating the user ' . $th->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password'  => 'required|string',
            'password'          => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return Message::error('Password does not match');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return Message::success('Password updated successfully');
        } catch (\Throwable $th) {
            return Message::error('An error occurred while updating the password ' . $th->getMessage());
        }
    }

    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page'          => 'nullable|numeric',
            'search'            => 'nullable|string',
            'most_followers'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Message::validator($validator->errors()->first(), isList: true);
        }

        $query = $this->user->query()
            ->withCount('followers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        if ($request->boolean('most_followers')) {
            $query->orderByDesc('followers_count');
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return Message::paginate('Users retrieved successfully', UserResource::collection($users));
    }
}
