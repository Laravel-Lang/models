<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Eloquent\Translation;

class Relation
{
    public static function initialize(Model $model): Model
    {
        if (blank($model->translations)) {
            $translations = $model->load('translations')->translations ?? new DBCollection();

            $model->setRelation('translations', $translations);
        }

        return $model;
    }

    public static function initializeLocale(Model $model, string $locale): Translation
    {
        return static::setAttributes($model, new (static::modelName($model))(), $locale);
    }

    public static function resolveKey(Model $model): void
    {
        $model->translations->each(
            fn (Translation $item) => $item->forceFill(['item_id' => $model->getKey()])
        );
    }

    public static function clear(Model $model): void
    {
        $model->setRelation('translations', new DBCollection());
    }

    protected static function setAttributes(Model $model, Translation $translation, string $locale): Translation
    {
        return $translation
            ->setAttribute('item_id', $model->getKey())
            ->setAttribute('locale', $locale);
    }

    protected static function modelName(Model $model): string
    {
        return get_class($model) . Config::shared()->models->suffix;
    }
}
