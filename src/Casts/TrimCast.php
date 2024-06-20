<?php

declare(strict_types=1);

namespace LaravelLang\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TrimCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_bool($value) || is_null($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }

        if ($value = trim((string) $value)) {
            return $value;
        }

        return null;
    }
}
