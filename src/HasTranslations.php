<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Models\Models\Translation;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait HasTranslations
{
    /*
     * Translatable columns
     */
    protected array $translatable = [];

    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            return $model->translation?->save() ?? $model;
        });

        static::deleting(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            return $model->translation()->delete();
        });
    }

    public function translation(): MorphOne
    {
        return $this->morphOne(Translation::class, 'model');
    }

    public function setTranslation(
        string $column,
        int|float|string|null $value,
        Locale|string|null $locale = null
    ): static {
        $this->translation->content->set($column, $value, $locale);

        return $this;
    }

    public function getTranslation(string $column, Locale|string|null $locale = null): int|float|string|null
    {
        return $this->translation->content->get($column, $locale);
    }
}
