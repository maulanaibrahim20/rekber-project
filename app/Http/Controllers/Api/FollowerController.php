<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowerController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }
    public function toggleFollow($uuidOrUsername)
    {
        DB::beginTransaction();
        try {
            $authUser = Auth::user();

            // Cari target user yang ingin di follow berdasarkan ID atau username
            $targetUser = $this->user->where('uuid', $uuidOrUsername)
                ->orWhere('username', $uuidOrUsername)
                ->firstOrFail();

            if ($authUser->id === $targetUser->id) {
                return Message::error('You cannot follow yourself');
            }

            $isFollowing = $authUser->following()
                ->where('following_id', $targetUser->id)
                ->exists();

            if ($isFollowing) {
                $authUser->following()->detach($targetUser->id);
                $message = 'Unfollowed successfully';
                $followed = false;
            } else {
                $authUser->following()->attach($targetUser->id);
                $message = 'Followed successfully';
                $followed = true;
            }

            DB::commit();

            return Message::success($message, [
                'followed' => $followed,
                'followers_count' => $targetUser->followers()->count(),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Failed to toggle follow: ' . $th->getMessage());
        }
    }

    public function followers($uuidOrUsername)
    {
        try {
            $user = $this->user->where('uuid', $uuidOrUsername)
                ->orWhere('username', $uuidOrUsername)
                ->firstOrFail();

            $followers = $user->followers()
                ->get()
                ->map(function ($follower) {
                    return [
                        'id' => $follower->id,
                        'name' => $follower->name,
                        'username' => $follower->username,
                    ];
                });

            return Message::success('Followers retrieved successfully', [
                'count' => $followers->count(),
                'followers' => $followers,
            ]);
        } catch (\Throwable $th) {
            return Message::error('Failed to retrieve followers: ' . $th->getMessage());
        }
    }

    public function following($uuidOrUsername)
    {
        try {
            $user = $this->user->where('uuid', $uuidOrUsername)
                ->orWhere('username', $uuidOrUsername)
                ->firstOrFail();

            $following = $user->following()
                ->get()
                ->map(function ($followed) {
                    return [
                        'id' => $followed->id,
                        'name' => $followed->name,
                        'username' => $followed->username,
                    ];
                });

            return Message::success('Following retrieved successfully', [
                'count' => $following->count(),
                'following' => $following,
            ]);
        } catch (\Throwable $th) {
            return Message::error('Failed to retrieve following: ' . $th->getMessage());
        }
    }
}
