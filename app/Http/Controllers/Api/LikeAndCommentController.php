<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Product;
use App\Models\ProductLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikeAndCommentController extends Controller
{
    protected $post, $postLike, $postComment;

    public function __construct()
    {
        $this->post = new Post();
        $this->postLike = new PostLike();
        $this->postComment = new PostComment();
    }
    public function toggleLike(Request $request, $uuid)
    {
        try {
            $user = Auth::user();
            $post = $this->post->where('uuid', $uuid)->orWhere('id', $uuid)->firstOrFail();

            DB::beginTransaction();

            $existingLike = $this->postLike->where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $message = 'Post unliked successfully';
                $liked = false;
            } else {
                $this->postLike->create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
                $message = 'Post liked successfully';
                $liked = true;
            }

            DB::commit();

            return Message::success($message, [
                'liked' => $liked,
                'likes_count' => $post->likes()->count(),
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
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        try {
            $post = $this->post->where('uuid', $uuid)->orWhere('id', $uuid)->first();

            if (!$post) {
                return Message::error('Post not found');
            }

            $comment = $this->postComment->create([
                'post_id'    => $post->id,
                'user_id'    => Auth::id(),
                'comment'    => $request->comment,
                'parent_id'  => $request->parent_id,
            ]);

            return Message::success('Comment created successfully', [
                'id'         => $comment->id,
                'comment'    => $comment->comment,
                'user'       => [
                    'id'       => $comment->user->id,
                    'name'     => $comment->user->name,
                    'username' => $comment->user->username,
                ],
                'created_at' => $comment->created_at,
            ]);
        } catch (\Throwable $th) {
            return Message::error('Failed to create comment: ' . $th->getMessage());
        }
    }
}
