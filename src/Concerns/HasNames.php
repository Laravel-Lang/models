<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Services\ModelConfig;

trait HasNames
{
    public function translationModelName(): string
    {
        return static::class . app(ModelConfig::class)->suffix;
    }

    public function getTranslationTable(): string
    {
        return (new ($this->translationModelName())())->getTable();
    }
}
