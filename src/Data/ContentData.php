<?php

declare(strict_types=1);

namespace LaravelLang\Models\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Concerns\HasStrings;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;

class ContentData implements Arrayable, Jsonable
{
    use HasStrings;

    public function __construct(
        protected array $locales
    ) {}

    public function set(
        string $column,
        array|ContentData|float|int|string|null $value,
        Locale|string|null $locale = null
    ): void {
        $locale = $locale ? $this->locale($locale) : $this->getDefault();

        $value = $this->trim($value);

        if ($value instanceof ContentData) {
            $this->locales[$column] = $value->getRaw($column);

            return;
        }

        is_array($value)
            ? $this->locales[$column]          = $value
            : $this->locales[$column][$locale] = $value;
    }

    public function get(string $column, Locale|string|null $locale = null): float|int|string|null
    {
        if ($locale) {
            return $this->locales[$column][$this->locale($locale)] ?? null;
        }

        return $this->locales[$column][$this->getDefault()]
            ?? $this->locales[$column][$this->getFallback()]
            ?? null;
    }

    public function has(string $column, Locale|string|null $locale = null): bool
    {
        if ($locale) {
            return isset($this->locales[$column][$this->locale($locale)]);
        }

        return isset($this->locales[$column][$this->getDefault()])
            || isset($this->locales[$column][$this->getFallback()]);
    }

    public function forget(string $column, Locale|string|null $locale = null): void
    {
        if ($locale) {
            unset($this->locales[$column][$this->locale($locale)]);

            return;
        }

        unset($this->locales[$column]);
    }

    public function getRaw(?string $path = null): mixed
    {
        return $path ? data_get($this->locales, $path) : $this->locales;
    }

    public function toJson($options = 0): ?string
    {
        if ($items = $this->toArray()) {
            return json_encode($items, $options);
        }

        return null;
    }

    public function toArray(): array
    {
        return collect($this->locales)
            ->map(fn (array $locale) => array_filter($locale, fn (mixed $value) => ! blank($value)))
            ->filter()
            ->all();
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
