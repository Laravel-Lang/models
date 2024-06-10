<?php

declare(strict_types=1);

namespace LaravelLang\Models\Data;

use Illuminate\Contracts\Support\Jsonable;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;

class ContentData implements Jsonable
{
    public function __construct(
        protected array $locales
    ) {}

    public function set(string $column, int|float|string|null $value, Locale|string|null $locale): void
    {
        $locale = $this->locale($locale);

        if (is_null($value) || $value === '') {
            unset($this->locales[$column][$locale]);

            return;
        }

        $this->locales[$column][$locale] = $value;
    }

    public function get(string $column, Locale|string|null $locale): int|float|string|null
    {
        $locale = $this->locale($locale);

        return $this->locales[$column][$locale] ?? null;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->locales, $options);
    }

    protected function locale(Locale|string|null $locale): string
    {
        return Locales::get($locale)->code ?? Locales::getDefault()->code;
    }
}
