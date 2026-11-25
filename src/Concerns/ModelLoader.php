<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Models\Services\RelationService;

use function in_array;

trait ModelLoader
{
    public static function bootModelLoader(): void
    {
        static::saved(static function (self $model): void {
            RelationService::resolveKey($model);

            $model->translations->each->save();
        });
    }

    public function initializeModelLoader(): void
    {
        if (isset($this->with['translations'])) {
            return;
        }

        if (in_array('translations', $this->with, true)) {
            return;
        }

        $this->with[] = 'translations';
    }
}
