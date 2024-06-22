<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use LaravelLang\Config\Facades\Config;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Concerns\HasValidations;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\Models\Services\Registry;
use LaravelLang\Models\Services\Relation;

use function app;
use function array_merge;
use function array_unique;
use function filled;
use function in_array;
use function is_iterable;

/**
 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasTranslations
{
    use HasValidations;

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

    public function initializeHasTranslations(): void
    {
        $this->with = array_unique(array_merge($this->with, ['translations']));
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::translationModelName(), 'item_id');
    }

    public function hasTranslated(string $column, Locale|LocaleData|string|null $locale = null): bool
    {
        $this->validateTranslation($column, $locale);

        return filled($this->getTranslation($column, $locale));
    }

    public function setTranslation(string $column, mixed $value, Locale|LocaleData|string|null $locale = null): static
    {
        $locale = $this->validateTranslation($column, $locale);

        Relation::initialize($this);

        TranslationHasBeenSetEvent::dispatch(
            $this,
            $column,
            $locale?->locale ?? null,
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
            $locale = $this->validateTranslation($column, $locale);

            $this->setTranslation($column, $value, $locale);
        }

        return $this;
    }

    public function getTranslation(string $column, Locale|LocaleData|string|null $locale = null): mixed
    {
        $data = $this->validateTranslation($column, $locale);

        if (! $locale) {
            $current  = Locales::getCurrent();
            $fallback = Locales::getFallback();

            return $this->translation($current)->getAttribute($column)
                ?? $this->translation($fallback)->getAttribute($column);
        }

        return $this->translation($data)->getAttribute($column);
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }

    public function forgetTranslation(Locale|LocaleData|string $locale): void
    {
        $locale = $this->validateTranslationLocale($locale);

        $this->translation($locale)->delete();
        $this->translations->forget($locale->locale->value);

        TranslationHasBeenForgetEvent::dispatch($this, $locale?->locale);
    }

    public function forgetAllTranslations(): void
    {
        $this->translations()->delete();

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

    public function setRelation($relation, $value): static
    {
        $this->relations[$relation] = match ($relation) {
            'translations' => $value->keyBy('locale'),
            default        => $value
        };

        return $this;
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
        return app(Registry::class)->get(__METHOD__, function () {
            return (new (static::translationModelName())())->translatable();
        });
    }

    protected function translation(?LocaleData $locale): Translation
    {
        $locale ??= Locales::getCurrent();

        if (! $this->translations->has($locale->locale->value)) {
            $this->translations->put($locale->locale->value, Relation::initializeLocale($this, $locale));
        }

        return $this->translations->get($locale->locale->value);
    }
}
