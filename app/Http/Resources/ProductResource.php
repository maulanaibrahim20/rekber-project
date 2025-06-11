<?php

namespace App\Http\Resources;

use App\Enum\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        $authId = Auth::user()->id ?? null;

        return [
            'uuid'        => $this->uuid,
            'user_id'     => $this->user_id,
            'name'        => $this->name,
            'description' => $this->description,
            'location'    => $this->location,
            'price'       => $this->price,
            'priority'    => $this->priority,
            'status'      => [
                'key'   => (string) $this->getRawOriginal('status'),
                'value' => Status::label('productStatus', $this->getRawOriginal('status')),
            ],
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'images'      => $this->images,
            'tags'        => $this->tags->pluck('name'),
            'like_count'    => $this->likes_count ?? $this->likes->count(),
            'comment_count' => $this->comments_count ?? $this->comments->count(),
            'is_liked' => $authId ? $this->isLikedByUser($authId) : false,
            'likes'    => $this->whenLoaded('likes', function () {
                return $this->likes->map(function ($like) {
                    return [
                        'id'       => $like->id,
                        'user_id'  => $like->user_id,
                        'liked_at' => $like->created_at,
                    ];
                });
            }),
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->map(function ($comment) {
                    return [
                        'id'            => $comment->id,
                        'user_id'       => $comment->user_id,
                        'comment_text'  => $comment->comment_text,
                        'created_at'    => $comment->created_at,
                        'user'          => $comment->user
                    ];
                });
            }),
            'user'        => $this->user,
        ];
    }
}
