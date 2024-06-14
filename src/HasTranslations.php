<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use LaravelLang\Models\Models\Translation;

/**
 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            $model->translation?->setAttribute('model_id', $model->getKey());
            $model->translation?->save();

            if (! $model->translation) {
                $model->setRelation('translation', $model->translation()->make());
            }
        });

        static::deleting(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            return $model->translation?->delete() ?? $model->translation()->delete();
        });

        if (method_exists(static::class, 'forceDeleted')) {
            static::forceDeleted(function (Model $model) {
                /** @var \LaravelLang\Models\HasTranslations $model */
                return $model->translation?->forceDelete() ?? $model->translation()->forceDelete();
            });
        }

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                /** @var \LaravelLang\Models\HasTranslations $model */
                $model->translation()->onlyTrashed()?->restore();
            });
        }
    }

    public function translation(): MorphOne
    {
        return $this->morphOne(Translation::class, 'model');
    }

    public function setTranslation(
        string $column,
        array|int|float|string|null|ContentData $value,
        Locale|string|null $locale = null
    ): static {
        $this->validateTranslationColumn($column, $locale, true);

        if (is_null($this->translation)) {
            $this->setRelation('translation', $this->translation()->make());
        }

        TranslationHasBeenSetEvent::dispatch(
            $this,
            $column,
            $locale?->value ?? $locale,
            $this->getTranslation($column, $locale),
            $value
        );

        $this->translation->content->set($column, $value, $locale);

        return $this;
    }

    public function getTranslation(string $column, Locale|string|null $locale = null): int|float|string|null
    {
        $this->validateTranslationColumn($column, $locale);

        return $this->translation?->content?->get($column, $locale);
    }

    public function hasTranslated(string $column, Locale|string|null $locale = null): bool
    {
        $this->validateTranslationColumn($column, $locale);

        return $this->translation->content?->has($column, $locale) ?? false;
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }

    public function forgetTranslation(string $column, Locale|string|null $locale = null): void
    {
        $this->validateTranslationColumn($column, $locale);

        $this->translation->content?->forget($column, $locale);
    }

    public function forgetAllTranslations(): void
    {
        $this->translation?->setAttribute('content', new ContentData([]));
    }

    public function translatable(): array
    {
        return [];
    }

    public function getAttribute($key): mixed
    {
        if ($this->isTranslatable($key)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    public function newInstance($attributes = [], $exists = false): static
    {
        $basic        = Arr::except($attributes, $this->translatable());
        $translatable = Arr::only($attributes, $this->translatable());

        $model = parent::newInstance($basic, $exists);

        foreach ($translatable as $key => $value) {
            $model->setTranslation($key, $value);
        }

        return $model;
    }

    protected function validateTranslationColumn(
        string $column,
        Locale|string|null $locale,
        bool $withInstalled = false
    ): void {
        if (! $this->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $this);
        }

        if ($locale && ! $withInstalled && ! Locales::isInstalled($locale)) {
            throw new UnavailableLocaleException($locale);
        }
    }
}
