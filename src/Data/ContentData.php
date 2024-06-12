<?php

declare(strict_types=1);

namespace LaravelLang\Models\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;

class ContentData implements Jsonable, Arrayable
{
    public function __construct(
        protected array $locales
    ) {}

    public function set(
        string $column,
        int|float|string|null $value,
        Locale|string|null $locale = null
    ): int|float|string|null {
        $locale = $locale ? $this->locale($locale) : $this->getDefault();

        $this->locales[$column][$locale] = $value;

        return $value;
    }

    public function get(string $column, Locale|string|null $locale = null): int|float|string|null
    {
        if ($locale) {
            return $this->locales[$column][$this->locale($locale)] ?? null;
        }

        return $this->locales[$column][$this->getDefault()]
            ?? $this->locales[$column][$this->getFallback()]
            ?? null;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toArray(): array
    {
        return array_filter(array_map(fn (array $locale) => array_filter($locale), $this->locales));
    }

    protected function locale(Locale|string|null $locale): string
    {
        if (! Locales::isInstalled($locale)) {
            throw new UnavailableLocaleException($locale);
        }

        return Locales::get($locale)->code;
    }

    protected function getDefault(): string
    {
        return Locales::getDefault()->code;
    }

    protected function getFallback(): string
    {
        return Locales::getFallback()->code;
    }
}
