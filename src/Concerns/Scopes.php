<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Locales\Facades\Locales;

/**
 * @mixin \LaravelLang\Models\Concerns\HasNames
 */
trait Scopes
{
    public function scopeTranslated(Builder $query): void
    {
        $query->has('translations');
    }

    public function scopeOrWhereTranslation(Builder $query, string $translationField, $value, ?string $locale = null): void
    {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas');
    }

    public function scopeOrWhereTranslationLike(Builder $query, string $translationField, $value, ?string $locale = null): void
    {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas', 'LIKE');
    }

    public function scopeWhereTranslation(Builder $query, string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '='): void
    {
        $query->$method('translations', function (Builder $query) use ($translationField, $value, $locale, $operator) {
            $query->where($this->getTranslationTable().'.'.$translationField, $operator, $value);

            if ($locale) {
                $query->where($this->getTranslationTable().'.locale', $operator, $locale);
            }
        });
    }

    public function scopeWhereTranslationLike(Builder $query, string $translationField, $value, ?string $locale = null): void
    {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    public function scopeOrderByTranslation(Builder $query, string $translationField, string $sortMethod = 'asc', ?string $locale = null): void
    {
        $table = $this->getTable();
        $translationTable = $this->getTranslationTable();
        $locale ??= Locales::getCurrent()->code;

        $query
            ->orderBy(
                (new ($this->translationModelName())())
                    ->query()
                    ->select($translationField)
                    ->where($translationTable . '.locale', $locale)
                    ->whereColumn($translationTable . '.item_id', $table . '.id'),
                $sortMethod
            );
    }

}
