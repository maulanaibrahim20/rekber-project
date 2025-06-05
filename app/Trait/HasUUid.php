<?php

namespace App\Trait;

use Illuminate\Support\Str;

trait HasUUid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}
