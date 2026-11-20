<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Services\ModelLangConfig;

trait HasNames
{
    public function translationModel(): string
    {
        return static::class . app(ModelLangConfig::class)->suffix;
    }

    public function translationQualifyColumn(string $column): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = $this->translationModel();

        return (new $class)->qualifyColumn($column);
    }
}
