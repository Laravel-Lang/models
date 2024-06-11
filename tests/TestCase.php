<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LaravelLang\Config\Enums\Name;
use LaravelLang\Config\ServiceProvider as ConfigServiceProvider;
use LaravelLang\Locales\ServiceProvider as LocalesServiceProvider;
use LaravelLang\Models\ServiceProvider as ModelsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Constants\LocaleValue;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function getPackageProviders($app): array
    {
        return [
            ModelsServiceProvider::class,
            LocalesServiceProvider::class,
            ConfigServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.locale', LocaleValue::LocaleMain);
        $config->set('app.fallback_locale', LocaleValue::LocaleFallback);

        //$config->set(Name::Hidden() . '.models.directory', __DIR__ . '/Fixtures/Models');
        $config->set(Name::Hidden() . '.models.directory', __DIR__ );
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
