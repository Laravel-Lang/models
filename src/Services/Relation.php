<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Eloquent\Translation;

class Relation
{
    public static function initializeModel(Model $model): void
    {
        if (blank($model->translations) && blank($model->load('translations')->translations)) {
            $model->setRelation('translations', new DBCollection());
        }

        static::locales()->each(function (LocaleData $locale) use ($model) {
            if (! $model->translations?->has($locale->code)) {
                $model->translations->put($locale->code, static::initializeLocale($model, $locale->code));
            }
        });
    }

    public static function initializeLocale(Model $model, string $locale): Translation
    {
        return (new (static::modelName($model))())
            ->setAttribute('item_id', $model->getKey())
            ->setAttribute('locale', $locale);
    }

    protected static function modelName(Model $model): string
    {
        return get_class($model) . Config::shared()->models->suffix;
    }

    protected static function locales(): Collection
    {
        return Locales::installed();
    }
}
