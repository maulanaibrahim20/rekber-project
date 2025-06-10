<?php

namespace App\Http\Controllers\Api;

use App\Enum\Status;
use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request, $username)
    {
        try {
            $statusMap = [
                'published' => 1,
                'draft'     => 2,
                'archived'  => 3,
                'blocked'   => 4,
            ];

            $statusString = $request->query('status', 'published');
            $statusCode = $statusMap[strtolower($statusString)] ?? 1;

            $user = User::with([
                'products' => function ($q) use ($statusCode) {
                    $q->where('status', $statusCode)
                        ->withCount(['likes', 'comments'])
                        ->orderByDesc('priority')
                        ->orderByDesc('created_at')
                        ->with('images');
                }
            ])->where('username', $username)->firstOrFail();

            if (!$user) {
                return Message::error('User not found');
            }

            $authId = Auth::id();

            $products = $user->products->map(function ($product) use ($authId) {
                return [
                    'id'            => $product->id,
                    'uuid'          => $product->uuid,
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'priority'      => $product->priority,
                    'status'        => $product->status,
                    'like_count'    => $product->likes_count,
                    'comment_count' => $product->comments_count,
                    'is_liked'      => $authId ? $product->isLikedByUser($authId) : false,
                    'images'        => $product->images,
                ];
            });

            return Message::success('User profile loaded', [
                'id'             => $user->id,
                'uuid'           => $user->uuid,
                'name'           => $user->name,
                'username'       => $user->username,
                'bio'            => $user->bio,
                'profile_picture' => $user->profile_picture,
                'product_count'  => $products->count(),
                'products'       => $products,
            ]);
        } catch (\Throwable $th) {
            return Message::error('User not found');
        }
    }


    public function pin($uuid)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $product = Product::where('uuid', $uuid)->orWhere('id', $uuid)
                ->where('user_id', $user->id)
                ->first();

            if (!$product) {
                return Message::error('Product not found or not owned by the user');
            }

            if ($product->priority == 1) {
                $product->priority = 0;
                $product->save();

                DB::commit();
                return Message::success('Product unpinned successfully');
            }

            $pinnedCount = Product::where('user_id', $user->id)
                ->where('priority', 1)
                ->count();

            if ($pinnedCount >= 3) {
                DB::rollBack();
                return Message::error('You can only pin up to 3 products');
            }

            $product->priority = 1;
            $product->save();

            DB::commit();
            return Message::success('Product pinned successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('An error occurred: ' . $th->getMessage());
        }
    }
}
