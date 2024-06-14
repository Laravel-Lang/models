<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

trait HasStrings
{
    protected function trim(mixed $value): mixed
    {
        if (is_string($value)) {
            return trim($value) ?: null;
        }

        return $value;
    }
}
