<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Exceptions\UnknownLocaleException;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\NativeLocaleNames\LocaleData;

class Validator
{
    /**
     * @param  Model|\LaravelLang\Models\HasTranslations  $model
     * @param  string  $column
     *
     * @throws AttributeIsNotTranslatableException
     * @return string
     */
    public static function column(Model $model, string $column): string
    {
        if (! $model->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $model);
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
