<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\HasTranslations;

use function collect;
use function ltrim;

class ClassMapService
{
    public static function translatable(): array
    {
        return collect(static::map())
            ->keys()
            ->filter(static fn (string $class) => static::isTranslatable($class))
            ->all();
    }

    public static function path(string $class): ?string
    {
        return collect(static::map())
            ->filter(static fn (string $path, string $name) => $name === ltrim($class, '\\'))
            ->first();
    }

    public static function available(): array
    {
        return collect(static::map())
            ->keys()
            ->filter(static fn (string $name) => static::canTranslatable($name))
            ->sort()
            ->values()
            ->all();
    }

    protected static function map(): array
    {
        $generator = static::generator();

        foreach (static::modelsPath() as $path) {
            $generator->scanPaths($path);
        }

        return $generator->getClassMap()->getMap();
    }

    protected static function generator(): ClassMapGenerator
    {
        return new ClassMapGenerator;
    }

    protected static function modelsPath(): array
    {
        return Arr::wrap(app(ModelLangConfig::class)->directories);
    }

    protected static function isTranslatable(string $class): bool
    {
        return ClassUsesService::usesTrait($class, HasTranslations::class);
    }

    protected static function canTranslatable(string $class): bool
    {
        return ClassUsesService::usesClass($class, Model::class)
            && ! ClassUsesService::usesClass($class, Translation::class);
    }
}
