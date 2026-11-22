<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\Models\Services\RelationService;

use function array_merge;
use function array_unique;

trait ModelLoader
{
    public static function bootModelLoader(): void
    {
        static::saved(
        /** @phpstan-param Model<\LaravelLang\Models\HasTranslations> $model */
            static function (Model $model): void {
                RelationService::resolveKey($model);

                $model->translations?->each?->save();
            }
        );
    }

    public function initializeModelLoader(): void
    {
        $this->with = array_unique(array_merge($this->with, ['translations']));
    }
}
