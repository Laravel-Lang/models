<?php

namespace Tests;

use Illuminate\Config\Repository;
use LaravelLang\Config\Enums\Name;
use LaravelLang\Config\ServiceProvider as ConfigServiceProvider;
use LaravelLang\Locales\ServiceProvider as LocalesServiceProvider;
use LaravelLang\Models\ServiceProvider as ModelsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\Locales;
use Tests\Constants\LocaleValue;

abstract class TestCase extends BaseTestCase
{
    use Locales;

    protected function getPackageProviders($app): array
    {
        return [
            ConfigServiceProvider::class,
            ModelsServiceProvider::class,
            LocalesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.locale', LocaleValue::LocaleMain);
        $config->set('app.fallback_locale', LocaleValue::LocaleFallback);

        $config->set(Name::Hidden() . '.models.directory', __DIR__ . '/Fixtures/Models');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
