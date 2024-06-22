<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;

class Validator
{
    /**
     * @param  Model|\LaravelLang\Models\HasTranslations  $model
     */
    public static function column(Model $model, string $column): string
    {
        if (! $model->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $model);
        }

        return $column;
    }

    public static function locale(Locale|LocaleData|string|null $value): ?LocaleData
    {
        $locale = static::resolveLocale($value);

        if ($value && ! Locales::isInstalled($value)) {
            throw new UnavailableLocaleException($locale->code ?? null);
        }

        return $locale;
    }

    protected static function resolveLocale(Locale|LocaleData|string|null $locale): ?LocaleData
    {
        return $locale ? Locales::get($locale) : null;
    }
}
