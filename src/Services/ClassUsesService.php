<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use function class_uses_recursive;
use function in_array;
use function is_a;

class ClassUsesService
{
    public static function usesClass(string $haystack, string $needle): bool
    {
        return is_a($haystack, $needle, true);
    }

    public static function usesTrait(string $haystack, string $needle): bool
    {
        return in_array($needle, class_uses_recursive($haystack), true);
    }
}
