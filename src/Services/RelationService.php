<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;
use LaravelLang\Models\Eloquent\Translation;
use TypeError;

use function blank;

class RelationService
{
    public static function initialize(Model $model): Model
    {
        if (blank($model->getRelationValue('translations'))) {
            $translations = $model->load('translations')->getRelation('translations') ?? new Collection;

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
        foreach (static::collection($model) as $translation) {
            if (! $translation instanceof Translation) {
                throw new TypeError('The translations relation must contain only translation models.');
            }

            $translation->forceFill(['item_id' => $model->getKey()]);
        }
    }

    /** @return SupportCollection<array-key, mixed> */
    public static function collection(Model $model): SupportCollection
    {
        $translations = $model->getRelationValue('translations');

        if (! $translations instanceof SupportCollection) {
            throw new TypeError('The translations relation must be a collection.');
        }

        return $translations;
    }

    public static function clear(Model $model): void
    {
        $model->setRelation('translations', new Collection);
    }

    protected static function setAttributes(Model $model, Translation $translation, string $locale): Translation
    {
        $translation->setAttribute('item_id', $model->getKey());
        $translation->setAttribute('locale', $locale);

        return $translation;
    }
}
