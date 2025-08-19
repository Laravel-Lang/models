<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

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
            $query->where($this->getTranslationsTable().'.'.$translationField, $operator, $value);

            if ($locale) {
                $query->where($this->getTranslationsTable().'.locale', $operator, $locale);
            }
        });
    }

    public function scopeWhereTranslationLike(Builder $query, string $translationField, $value, ?string $locale = null): void
    {
        $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    protected function getTranslationsTable(): string
    {
        return (new ($this->translationModelName())())->getTable();
    }
}
