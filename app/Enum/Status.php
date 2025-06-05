<?php

namespace App\Enum;

class Status
{
    public static function __callStatic($method, $args)
    {
        return config("rekber.$method", []);
    }

    public static function label(string $group, int $value): string
    {
        $list = config("rekber.$group", []);
        return $list[$value] ?? 'UNKNOWN';
    }

    public static function fromString(string $group, string $label): ?int
    {
        $map = array_flip(config("rekber.$group", []));
        return $map[strtoupper($label)] ?? null;
    }

    public static function options(string $group): array
    {
        return config("rekber.$group", []);
    }
}
