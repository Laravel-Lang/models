<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use LaravelLang\Config\Facades\Config;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use LaravelLang\Models\Services\Registry;
use LaravelLang\Models\Services\Relation;

use function filled;
use function in_array;
use function is_iterable;

/**
 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            Relation::resolveKey($model);

            $model->translations?->each?->save();
        });
    }

    protected static function translationModelName(): string
    {
        return static::class . Config::shared()->models->suffix;
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::translationModelName(), 'item_id')->afterQuery(
            fn (Collection $items) => $items->keyBy('locale')
        );
    }

    public function hasTranslated(string $column, Locale|string|null $locale = null): bool
    {
        $this->validateTranslation($column, $locale);

        return filled($this->getTranslation($column, $locale));
    }

    public function setTranslation(string $column, mixed $value, Locale|string|null $locale = null): static
    {
        $this->validateTranslation($column, $locale);

        Relation::initialize($this);

        TranslationHasBeenSetEvent::dispatch(
            $this,
            $column,
            $locale?->value ?? $locale,
            $this->getTranslation($column, $locale),
            $value
        );

        $this->translation($locale)->setAttribute($column, $value);

        return $this;
    }

    public function setTranslations(
        string $column,
        iterable $values
    ): static {
        foreach ($values as $locale => $value) {
            $this->validateTranslation($column, $locale);

            $this->setTranslation($column, $value, $locale);
        }

        return $this;
    }

    public function getTranslation(string $column, Locale|string|null $locale = null): mixed
    {
        $this->validateTranslation($column, $locale);

        if (! $locale) {
            $current  = Locales::getCurrent()->code;
            $fallback = Locales::getFallback()->code;

            return $this->translation($current)->getAttribute($column)
                ?? $this->translation($fallback)->getAttribute($column);
        }

        return $this->translation($locale)->getAttribute($column);
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }

    public function forgetTranslation(Locale|string $locale): void
    {
        $this->validateTranslationLocale($locale);

        $locale = Locales::get($locale)->code;

        $this->translation($locale)->delete();
        $this->translations->forget($locale);

        TranslationHasBeenForgetEvent::dispatch($this, $locale);
    }

    public function forgetAllTranslations(): void
    {
        $this->translations->each->delete();

        Relation::clear($this);

        AllTranslationsHasBeenForgetEvent::dispatch($this);
    }

    public function getAttribute($key): mixed
    {
        if ($this->isTranslatable($key)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value): Model
    {
        if ($this->isTranslatable($key)) {
            return $this->setTranslation($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function newInstance($attributes = [], $exists = false): static
    {
        $basic        = Arr::except($attributes, $this->translatable());
        $translatable = Arr::only($attributes, $this->translatable());

        $model = parent::newInstance($basic, $exists);

        foreach ($translatable as $key => $value) {
            is_iterable($value)
                ? $model->setTranslations($key, $value)
                : $model->setTranslation($key, $value);
        }

        return $model;
    }

    public function translatable(): array
    {
        return Registry::get(__METHOD__, function () {
            return (new (static::translationModelName())())->translatable();
        });
    }

    protected function translation(Locale|string|null $locale = null): Translation
    {
        $code = Locales::get($locale)->code;

        if (! $this->translations->has($code)) {
            $this->translations->put($code, Relation::initializeLocale($this, $code));
        }

        return $this->translations->get($code);
    }

    protected function validateTranslation(string $column, Locale|string|null $locale): void
    {
        $this->validateTranslationColumn($column);
        $this->validateTranslationLocale($locale);
    }

    protected function validateTranslationColumn(string $column): void
    {
        if (! $this->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $this);
        }
    }

    protected function validateTranslationLocale(Locale|string|null $locale): void
    {
        if ($locale && ! Locales::isInstalled($locale)) {
            throw new UnavailableLocaleException($locale);
        }
    }
}
