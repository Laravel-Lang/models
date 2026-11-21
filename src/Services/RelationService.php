<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaravelLang\Models\Eloquent\Translation;

use function blank;

class RelationService
{
    public static function initialize(Model $model): Model
    {
        if (blank($model->getRelationValue('translations'))) {
            $translations = $model->load('translations')->translations ?? new Collection;

            $model->setRelation('translations', $translations);
        }

        return $model;
    }

    /** @param HasMany<Translation, *> $translations */
    public static function initializeLocale(Model $model, string $locale, HasMany $translations): Translation
    {
        if ($item = $translations->where('locale', $locale)->first()) {
            return static::setAttributes($model, $item, $locale);
        }

        $class = $translations->getRelated()::class;

        return static::setAttributes($model, new $class, $locale);
    }

    public static function resolveKey(Model $model): void
    {
        $model->getRelationValue('translations')->each(
            fn (Translation $item) => $item->forceFill(['item_id' => $model->getKey()])
        );
    }

    public static function clear(Model $model): void
    {
        $model->setRelation('translations', new Collection);
    }

    protected static function setAttributes(Model $model, Translation $translation, string $locale): Translation
    {
        return $translation
            ->setAttribute('item_id', $model->getKey())
            ->setAttribute('locale', $locale);
    }
}
