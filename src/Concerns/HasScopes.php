<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Locales\Facades\Locales;
use SortDirection;

/** @mixin HasNames */
trait HasScopes
{
    protected function scopeTranslated(Builder $query): void
    {
        $query->has('translations');
    }

    protected function scopeOrWhereTranslation(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas');
    }

    protected function scopeOrWhereTranslationLike(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas', 'LIKE');
    }

    protected function scopeWhereTranslation(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
        string $method = 'whereHas',
        string $operator = '=',
    ): void {
        $query->$method('translations', function (Builder $query) use ($translationField, $value, $locale, $operator): void {
            $query->where($this->translationQualifyColumn($translationField), $operator, $value);

            if ($locale) {
                $normalized = Locales::fromAlias($locale);

                $query->where($this->translationQualifyColumn('locale'), $operator, $normalized);
            }
        });
    }

    protected function scopeWhereTranslationLike(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    protected function scopeOrderByTranslation(
        Builder $query,
        string $translationField,
        SortDirection|string $sortMethod = 'asc',
        ?string $locale = null,
    ): void {
        $normalized = $locale
            ? Locales::fromAlias($locale)
            : Locales::current(false);

        if ($sortMethod instanceof SortDirection) {
            $sortMethod = $sortMethod === SortDirection::Descending ? 'desc' : 'asc';
        }

        $query
            ->orderBy(
                (new ($this->translationModel())())
                    ->query()
                    ->select($translationField)
                    ->where($this->translationQualifyColumn('locale'), $normalized)
                    ->whereColumn($this->translationQualifyColumn('item_id'), $this->qualifyColumn('id')),
                $sortMethod
            );
    }
}
