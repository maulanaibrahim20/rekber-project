<?php

namespace App\Helper;

use Illuminate\Support\Number;

class Formatter
{
    public static function rupiah($value, $fractionDigits = 0)
    {
        return Number::currency(
            $value,
            'IDR',
            'id',
            $fractionDigits,
        );
    }
}
