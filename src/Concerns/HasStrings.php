<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use function is_string;
use function trim;

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
