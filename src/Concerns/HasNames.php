<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Exceptions\InvalidTranslationModelException;
use LaravelLang\Models\Eloquent\Translation;

use function is_a;

trait HasNames
{
    public function translationModel(): string
    {
        return static::class . app(ModelLangConfig::class)->suffix;
    }

    public function translationQualifyColumn(string $column): string
    {
        $class = $this->translationClass();

        return (new $class)->qualifyColumn($column);
    }

    /** @return class-string<Translation> */
    protected function translationClass(): string
    {
        $class = $this->translationModel();

        if (! is_a($class, Translation::class, true)) {
            throw new InvalidTranslationModelException($class);
        }

        return $class;
    }
}
