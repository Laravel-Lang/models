<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Closure;
use LaravelLang\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Exceptions\UnknownLocaleException;
use LaravelLang\LocaleList\Locale;
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\Locales\Facades\Locales;

class ValidatorService
{
    /**
     * @param  Closure(): array<array-key, string>  $translatable
     *
     * @throws AttributeIsNotTranslatableException
     */
    public static function column(string $column, bool $isTranslatable, Closure $translatable): string
    {
        if (! $isTranslatable) {
            throw new AttributeIsNotTranslatableException($column, $translatable());
        }

        return $column;
    }

    public static function locale(Locale|LocaleData|string|null $locale): ?LocaleData
    {
        if ($locale && ! Locales::isInstalled($locale)) {
            throw new UnknownLocaleException($locale);
        }

        return null;
    }
}
