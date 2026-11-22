<?php

declare(strict_types=1);

namespace LaravelLang\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use TypeError;

use function is_bool;
use function is_null;
use function is_numeric;
use function is_scalar;
use function trim;

/** @implements CastsAttributes<bool|float|int|string, bool|float|int|string|Stringable> */
final class TrimStringCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): bool|float|int|string|null
    {
        if (is_scalar($value) || $value === null) {
            return $value;
        }

        throw new TypeError('The stored value must be a scalar or null.');
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): bool|float|int|string|null
    {
        if (is_bool($value) || is_numeric($value) || is_null($value)) {
            return $value;
        }

        if ($value = trim((string) $value)) {
            return $value;
        }

        return null;
    }
}
