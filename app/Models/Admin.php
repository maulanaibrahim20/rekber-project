<?php

namespace App\Models;

use App\Enum\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $guarded = ['id'];

    // public function getStatusAttribute($value)
    // {
    //     return [
    //         'key'   => (string) $value,
    //         'value' => Status::label('userStatus', $value),
    //     ];
    // }
}
