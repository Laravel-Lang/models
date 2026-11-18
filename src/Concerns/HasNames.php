<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Services\ModelConfig;

trait HasNames
{
    public function translationModel(): string
    {
        return static::class . app(ModelConfig::class)->suffix;
    }

    public function translationQualifyColumn(string $column): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = $this->translationModel();

        return (new $class)->qualifyColumn($column);
    }
}
