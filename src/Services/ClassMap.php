<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Composer\ClassMapGenerator\ClassMapGenerator;
use DragonCode\Support\Facades\Instances\Instance;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\HasTranslations;

use function collect;
use function ltrim;

class ClassMap
{
    public static function get(): array
    {
        return collect(static::map())
            ->keys()
            ->filter(static fn (string $class) => static::isTranslatable($class))
            ->all();
    }

    public static function find(string $value): array
    {
        return collect(static::map())
            ->keys()
            ->filter(static fn (string $class) => static::contains($class, $value))
            ->all();
    }

    public static function path(string $class): ?string
    {
        return collect(static::map())
            ->filter(static fn (string $path, string $name) => $name === ltrim($class, '\\'))
            ->first();
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
        return new ClassMapGenerator();
    }

    protected static function modelsPath(): array
    {
        return Arr::wrap(Config::hidden()->models->directory);
    }

    protected static function isTranslatable(string $class): bool
    {
        return Instance::of($class, HasTranslations::class);
    }

    protected static function contains(string $class, string $needle): bool
    {
        return Str::of($class)->lower()->contains(strtolower($needle));
    }
}
