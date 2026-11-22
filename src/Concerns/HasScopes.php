<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Exceptions\InvalidSortDirectionException;
use LaravelLang\Locales\Facades\Locales;
use SortDirection;

use function strtolower;

/** @mixin HasNames */
trait HasScopes
{
    /** @param  Builder<static>  $query */
    protected function scopeTranslated(Builder $query): void
    {
        $query->has('translations');
    }

    /** @param  Builder<static>  $query */
    protected function scopeOrWhereTranslation(
        Builder $query,
        string $translationField,
        mixed $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas');
    }

    /** @param  Builder<static>  $query */
    protected function scopeOrWhereTranslationLike(
        Builder $query,
        string $translationField,
        mixed $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas', 'LIKE');
    }

    /** @param  Builder<static>  $query */
    protected function scopeWhereTranslation(
        Builder $query,
        string $translationField,
        mixed $value,
        ?string $locale = null,
        string $method = 'whereHas',
        string $operator = '=',
    ): void {
        $query->$method(
            'translations',
            function (Builder $query) use ($translationField, $value, $locale, $operator): void {
                $query->where($this->translationQualifyColumn($translationField), $operator, $value);

                if ($locale) {
                    $normalized = Locales::fromAlias($locale);

                    $query->where($this->translationQualifyColumn('locale'), $operator, $normalized);
                }
            }
        );
    }

    /** @param  Builder<static>  $query */
    protected function scopeWhereTranslationLike(
        Builder $query,
        string $translationField,
        mixed $value,
        ?string $locale = null,
    ): void {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    /** @param  Builder<static>  $query */
    protected function scopeOrderByTranslation(
        Builder $query,
        string $translationField,
        SortDirection|string $sortMethod = 'asc',
        ?string $locale = null,
    ): void {
        $normalized = $locale
            ? Locales::fromAlias($locale)
            : Locales::current(false);

        $direction = match ($sortMethod instanceof SortDirection ? $sortMethod : strtolower($sortMethod)) {
            SortDirection::Ascending, 'asc'   => 'asc',
            SortDirection::Descending, 'desc' => 'desc',
            default                           => throw new InvalidSortDirectionException,
        };

        $query
            ->orderBy(
                (new ($this->translationClass())())
                    ->query()
                    ->select($translationField)
                    ->where($this->translationQualifyColumn('locale'), $normalized)
                    ->whereColumn($this->translationQualifyColumn('item_id'), $this->qualifyColumn('id')),
                $direction
            );
    }
}
