<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Locales\Facades\Locales;
use SortDirection;

/** @mixin \LaravelLang\Models\Concerns\HasNames */
trait HasScopes
{
    public function scopeTranslated(Builder $query): void
    {
        $query->has('translations');
    }

    public function scopeOrWhereTranslation(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas');
    }

    public function scopeOrWhereTranslationLike(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas', 'LIKE');
    }

    public function scopeWhereTranslation(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
        string $method = 'whereHas',
        string $operator = '=',
    ): void {
        $query->$method('translations', function (Builder $query) use ($translationField, $value, $locale, $operator) {
            $query->where($this->translationQualifyColumn($translationField), $operator, $value);

            if ($locale) {
                $normalized = Locales::fromAlias($locale);

                $query->where($this->translationQualifyColumn('locale'), $operator, $normalized);
            }
        });
    }

    public function scopeWhereTranslationLike(
        Builder $query,
        string $translationField,
        $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    public function scopeOrderByTranslation(
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
