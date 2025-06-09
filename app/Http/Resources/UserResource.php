<?php

namespace App\Http\Resources;

use App\Enum\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "username" => $this->username,
            "linkname" => $this->linkname,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "phone" => $this->phone,
            "birth_date" => $this->birth_date,
            "address" =>    $this->address,
            "bio" => $this->bio,
            "profile_picture" => $this->profile_picture,
            "gender" => $this->gender,
            "is_private" => $this->is_private,
            'status'      => [
                'key'   => (string) $this->getRawOriginal('status'),
                'value' => Status::label('productStatus', $this->getRawOriginal('status')),
            ],
        ];
    }
}
