<?php

namespace App\Models;

use App\Enum\Status;
use App\Trait\HasUUid;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUUid;
    protected $guarded = ['id'];

    // Value Attribute dari productStatus in config/rekber.php
    // public function getStatusAttribute($value)
    // {
    //     return [
    //         'key'   => (string) $value,
    //         'value' => Status::label('productStatus', $value),
    //     ];
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function likes()
    {
        return $this->hasMany(ProductLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ProductComments::class);
    }

    // Untuk cek apakah produk ini disukai oleh user tertentu
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }
}
