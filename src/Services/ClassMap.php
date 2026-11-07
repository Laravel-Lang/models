<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\HasTranslations;

use function class_exists;
use function class_uses;
use function collect;
use function is_a;
use function ltrim;

class ClassMap
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
        return Arr::wrap(Config::hidden()->models->directory);
    }

    protected static function isTranslatable(string $class): bool
    {
        return class_exists($class) && static::usesTrait($class, HasTranslations::class);
    }

    protected static function canTranslatable(string $class): bool
    {
        return is_a($class, Model::class, true)
            && ! is_a($class, Translation::class, true);
    }

    /** Checks the traits used by the class itself, including the traits nested in them. */
    protected static function usesTrait(string $class, string $trait): bool
    {
        foreach (class_uses($class) as $used) {
            if ($used === $trait || static::usesTrait($used, $trait)) {
                return true;
            }
        }

        return false;
    }
}
