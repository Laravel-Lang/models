<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use LaravelLang\Config\Facades\Config;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use LaravelLang\Models\Services\Registry;
use LaravelLang\Models\Services\Relation;

/**
 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::retrieved(function (Model $model) {
            Relation::initializeModel($model);
        });

        static::saved(function (Model $model) {
            /** @var HasTranslations|Model $model */
            Relation::initializeModel($model);

            $model->translations?->each?->save();
        });
        //
        //    static::deleting(function (Model $model) {
        //        // @var \LaravelLang\Models\HasTranslations $model
        //        return $model->translation?->delete() ?? $model->translation()->delete();
        //    });
        //
        //    if (method_exists(static::class, 'forceDeleted')) {
        //        static::forceDeleted(function (Model $model) {
        //            // @var \LaravelLang\Models\HasTranslations $model
        //            return $model->translation?->forceDelete() ?? $model->translation()->forceDelete();
        //        });
        //    }
        //
        //    if (method_exists(static::class, 'restored')) {
        //        static::restored(function (Model $model) {
        //            // @var \LaravelLang\Models\HasTranslations $model
        //            $model->translation()->onlyTrashed()?->restore();
        //        });
        //    }
    }

    protected static function translationModelName(): string
    {
        return static::class . Config::shared()->models->suffix;
    }

    public function initializeHasTranslations(): void
    {
        $this->with = array_unique($this->with + ['translations']);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::translationModelName(), 'item_id');
    }
    //
    //public function hasTranslated(string $column, Locale|string|null $locale = null): bool
    //{
    //    $this->validateTranslationColumn($column, $locale);
    //
    //    return $this->translation->content?->has($column, $locale) ?? false;
    //}
    //
    //
    public function setTranslation(
        string $column,
        array|float|int|string|null $value,
        Locale|string|null $locale = null
    ): static {
        $this->validateTranslationColumn($column, $locale);

        Relation::initializeModel($this);

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
        array $values
    ): static {
        foreach ($values as $locale => $value) {
            $this->validateTranslationColumn($column, $locale);

            $this->setTranslation($column, $value, $locale);
        }

        return $this;
    }

    public function getTranslation(string $column, Locale|string|null $locale = null): float|int|string|null
    {
        $this->validateTranslationColumn($column, $locale);

        if (! $locale) {
            $current  = Locales::getCurrent()->code;
            $fallback = Locales::getFallback()->code;

            return $this->translation($current)?->getAttribute($column)
                ?? $this->translation($fallback)?->getAttribute($column);
        }

        return $this->translation($locale)?->getAttribute($column);
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }
    //
    //public function forgetTranslation(string $column, Locale|string|null $locale = null): void
    //{
    //    $this->validateTranslationColumn($column, $locale);
    //
    //    $this->translation->content?->forget($column, $locale);
    //
    //    TranslationHasBeenForgetEvent::dispatch($this, $column, $locale?->value ?? $locale);
    //}
    //
    //public function forgetAllTranslations(): void
    //{
    //    $this->translation?->setAttribute('content', new ContentData([]));
    //
    //    AllTranslationsHasBeenForgetEvent::dispatch($this);
    //}
    //

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
            is_array($value)
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

    protected function translation(Locale|string|null $locale = null): ?Translation
    {
        return $this->translations->get(
            Locales::get($locale)->code
        );
    }

    protected function validateTranslationColumn(string $column, Locale|string|null $locale): void
    {
        if (! $this->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $this);
        }

        if ($locale && ! Locales::isInstalled($locale)) {
            throw new UnavailableLocaleException($locale);
        }
    }
}
