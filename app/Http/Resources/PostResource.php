<?php

namespace App\Http\Resources;

use App\Enum\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    protected function mapComment($comment)
    {
        return [
            'id'        => $comment->id,
            'comment'   => $comment->comment,
            'user'      => [
                'id'       => $comment->user->id ?? null,
                'name'     => $comment->user->name ?? null,
                'username' => $comment->user->username ?? null,
            ],
            'created_at' => $comment->created_at->toDateTimeString(),
            'children'   => $comment->children->map(function ($child) {
                return $this->mapComment($child); // Rekursif
            }),
        ];
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authId = Auth::user()->id ?? null;

        return [
            'id'        => $this->id,
            'uuid'      => $this->uuid,
            'user'      => [
                'id'       => $this->user->id ?? null,
                'name'     => $this->user->name ?? null,
                'username' => $this->user->username ?? null,
            ],
            'caption'   => $this->caption,
            'location'  => $this->location,
            'status'    => [
                'key'   => (string) $this->getRawOriginal('status'),
                'value' => Status::label('postStatus', $this->getRawOriginal('status')),
            ],
            'media' => $this->media->map(function ($media) {
                return [
                    'file_url' => asset('storage/' . $media->file_url),
                    'file_type' => $media->file_type,
                    'order' => $media->media_order,
                ];
            }),
            'media_thumbnail' => $this->media->first() ? asset('storage/' . $this->media->first()->file_url) : null,
            'tags'      => $this->tags->pluck('name'),
            'likes'     => [
                'count' => $this->likes->count(),
                'liked_by_me' => $this->likes->contains('user_id', $authId),
            ],
            'comments_count' => $this->comments_count ?? $this->comments->count(),
            'is_liked' => $authId ? $this->isLikedByUser($authId) : false,
            'comments' => $this->comments->map(function ($comment) {
                return $this->mapComment($comment);
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
