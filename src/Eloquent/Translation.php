<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent;

use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

use function array_filter;
use function mb_strtolower;

#[WithoutTimestamps]
abstract class Translation extends Model
{
    public function translatable(): array
    {
        return array_filter($this->getFillable(), static fn (string $column): bool => mb_strtolower($column) !== 'locale');
    }

    protected function casts(): array
    {
        return $this->casts;
    }
}
