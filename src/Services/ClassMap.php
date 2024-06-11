<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Composer\ClassMapGenerator\ClassMapGenerator;
use DragonCode\Support\Facades\Instances\Instance;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\HasTranslations;

class ClassMap
{
    public static function get(): array
    {
        return collect(static::map())
            ->keys()
            ->filter(static fn (string $class) => static::isTranslatable($class))
            ->all();
    }

    protected static function map(): array
    {
        return ClassMapGenerator::createMap(static::path());
    }

    protected static function path(): string
    {
        return Config::hidden()->models->directory;
    }

    protected static function isTranslatable(string $class): bool
    {
        return Instance::of($class, HasTranslations::class);
    }
}
