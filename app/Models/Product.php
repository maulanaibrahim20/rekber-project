<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

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
}
