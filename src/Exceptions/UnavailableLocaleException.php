<?php

declare(strict_types=1);

namespace LaravelLang\Models\Exceptions;

use Exception;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;

class UnavailableLocaleException extends Exception
{
    public function __construct(Locale|string|null $locale)
    {
        $locale = $locale->value ?? $locale;

        $available = Locales::installed()->pluck('locale.code')->filter()->implode(', ');

        parent::__construct(
            "Cannot set translation for `$locale` locale as it's not on of the installed locales: `$available`.",
            500
        );
    }
}
