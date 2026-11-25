<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

use function array_filter;
use function mb_strtolower;

abstract class Translation extends Model
{
    public $timestamps = false;

    /** @return array<array-key, string> */
    public function translatable(): array
    {
        return array_filter(
            $this->getFillable(),
            static fn (string $column): bool => mb_strtolower($column) !== 'locale'
        );
    }

    /** @return array<array-key, mixed> */
    protected function casts(): array
    {
        return $this->casts;
    }
}
