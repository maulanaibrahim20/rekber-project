<?php

namespace App\Http\Controllers\Api;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\PostComment;
use App\Models\ProductComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $productComment, $postComment;

    public function __construct()
    {
        $this->productComment = new ProductComments();
        $this->postComment = new PostComment();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required|exists:products,id',
            'per_page'      => 'nullable',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        $data = $this->productComment->where('product_id', $request->product_id)->paginate($request->per_page ?? 10);

        return Message::paginate('Comments retrieved successfully', $data);
    }

    public function indexPostComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id'       => 'required|exists:posts,id',
            'per_page'      => 'nullable',
        ]);

        if ($validator->fails()) {
            return Message::validator('Validation failed', $validator->errors());
        }

        $data = $this->postComment->where('post_id', $request->post_id)->with('user')->paginate($request->per_page ?? 10);

        $data->map(function ($comment) {
            return [
                'id'         => $comment->id,
                'comment'    => $comment->comment,
                'user'       => [
                    'id'       => $comment->user->id,
                    'name'     => $comment->user->name,
                    'username' => $comment->user->username,
                ]
            ];
        });
        return Message::paginate('Comments retrieved successfully', CommentResource::collection($data));
    }
}
