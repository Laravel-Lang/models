<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Models\Translation;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            return $model->translation?->save();
        });

        static::deleting(function (Model $model) {
            /** @var \LaravelLang\Models\HasTranslations $model */
            return $model->translation?->delete() ?? $model->translation()->delete();
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
        $this->validateTranslationColumn($column);

        if (is_null($this->translation)) {
            $this->setRelation('translation', $this->translation()->make());
        }

        $this->translation->content->set($column, $value, $locale);

        return $this;
    }

    public function getTranslation(string $column, Locale|string|null $locale = null): int|float|string|null
    {
        $this->validateTranslationColumn($column);

        return $this->translation?->content?->get($column, $locale);
    }

    public function hasTranslated(string $column, Locale|string|null $locale = null): bool
    {
        $this->validateTranslationColumn($column);

        return $this->translation->content?->has($column, $locale) ?? false;
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }

    public function forgetTranslation(string $column, ?string $locale = null): static
    {
        // TODO: write this one

        return $this;
    }

    public function forgetAllTranslations(): void
    {
            $this->translation?->delete() ?? $this->translation()->delete();
    }

    public function translatable(): array
    {
        return [];
    }

    protected function validateTranslationColumn(string $column): void
    {
        if (! $this->isTranslatable($column)) {
            throw new AttributeIsNotTranslatableException($column, $this);
        }
    }
}
