<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Models\Services\Validator;

trait HasValidations
{
    protected function validateTranslation(string $column, Locale|LocaleData|string|null $locale): ?LocaleData
    {
        $this->validateTranslationColumn($column);

        return $this->validateTranslationLocale($locale);
    }

    protected function validateTranslationColumn(string $column): string
    {
        return Validator::column($this, $column);
    }

    protected function validateTranslationLocale(Locale|LocaleData|string|null $locale): ?LocaleData
    {
        return Validator::locale($locale);
    }
}
