<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Closure;

class Registry
{
    public static array $values = [];

    public static function get(string $key, Closure $callback): mixed
    {
        return static::$values[$key] ?? $callback();
    }
}
