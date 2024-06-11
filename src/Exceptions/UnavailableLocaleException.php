<?php

declare(strict_types=1);

namespace LaravelLang\Models\Exceptions;

use Exception;
use LaravelLang\LocaleList\Locale;

class UnavailableLocaleException extends Exception
{
    public function __construct(Locale|string|null $locale)
    {
        $locale = $locale->value ?? $locale;

        parent::__construct("Unknown locale code:\"$locale\"", 500);
    }
}
