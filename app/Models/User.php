<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\Status;
use App\Trait\HasUUid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUUid;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'linkname',
        'phone',
        'address',
        'bio',
        'profile_picture',
        'gender',
        'is_private',
        'birth_date',
        'status',
        // 'email_verified_at',
        // 'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_private' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    public function getStatusAttribute($value)
    {
        return [
            'key'   => (string) $value,
            'value' => Status::label('userStatus', $value),
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function likes()
    {
        return $this->hasMany(ProductLike::class);
    }

    // Comments yang dibuat user ini
    public function comments()
    {
        return $this->hasMany(ProductComments::class);
    }
}
