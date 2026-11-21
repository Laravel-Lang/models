<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\HasTranslations;

use function collect;
use function ltrim;

class ClassMapService
{
    /** @return array<int, class-string> */
    public static function translatable(): array
    {
        return (new Collection(static::map()))
            ->keys()
            ->filter(static fn (string $class): bool => static::isTranslatable($class))
            ->all();
    }

    public static function path(string $class): ?string
    {
        return (new Collection(static::map()))
            ->filter(static fn (string $path, string $name): bool => $name === ltrim($class, '\\'))
            ->first();
    }

    /** @return list<class-string> */
    public static function available(): array
    {
        return (new Collection(static::map()))
            ->keys()
            ->filter(static fn (string $name): bool => static::canTranslatable($name))
            ->sort()
            ->values()
            ->all();
    }

    /** @return array<class-string, non-empty-string> */
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

    /** @return array<array-key, string> */
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
