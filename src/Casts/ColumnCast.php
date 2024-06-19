<?php

declare(strict_types=1);

namespace LaravelLang\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ColumnCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array {}

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
    }

    protected function locale(): string
    {
        return app()->getLocale();
    }
}
