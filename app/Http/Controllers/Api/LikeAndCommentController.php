<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikeAndCommentController extends Controller
{

    public function toggleLike(Request $request, $uuid)
    {
        try {
            $user = Auth::user();
            $product = Product::where('uuid', $uuid)->orWhere('id', $uuid)->firstOrFail();

            DB::beginTransaction();

            $existingLike = ProductLike::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $message = 'Product unliked successfully';
                $liked = false;
            } else {
                ProductLike::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                ]);
                $message = 'Product liked successfully';
                $liked = true;
            }

            DB::commit();

            return Message::success($message, [
                'liked' => $liked,
                'likes_count' => $product->likes()->count(),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Message::error('Failed to toggle like: ' . $th->getMessage());
        }
    }

    public function comment(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        try {
            $product = Product::where('uuid', $uuid)->firstOrFail();

            $comment = $product->comments()->create([
                'user_id' => Auth::id(),
                'comment_text' => $request->comment,
            ]);

            return Message::success('Comment created successfully', [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'username' => $comment->user->username,
                ],
                'created_at' => $comment->created_at,
            ]);
        } catch (\Throwable $th) {
            return Message::error('Failed to create comment: ' . $th->getMessage());
        }
    }
}
