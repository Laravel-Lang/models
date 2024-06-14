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
        parent::__construct(
            sprintf(
                'Cannot set translation for `%s` locale as it\'s not on of the installed locales: `%s`.',
                $locale->value ?? $locale,
                $this->available()
            )
        );
    }

    protected function available(): string
    {
        return Locales::installed()->pluck('locale.code')->filter()->implode(', ');
    }
}
