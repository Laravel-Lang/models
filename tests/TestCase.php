<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\ParallelTesting;
use LaravelLang\Config\Enums\Name;
use LaravelLang\Config\ServiceProvider as ConfigServiceProvider;
use LaravelLang\Locales\ServiceProvider as LocalesServiceProvider;
use LaravelLang\Models\ServiceProvider as ModelsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\Locales;
use Tests\Constants\FakeValue;

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
        tap($app['config'], function (Repository $config) {
            $config->set('app.locale', FakeValue::LocaleMain);
            $config->set('app.fallback_locale', FakeValue::LocaleFallback);

            $config->set(Name::Hidden() . '.models.directory', [
                __DIR__ . '/Fixtures/Models',
                base_path('app'),
            ]);

            $config->set('testing.parallel_token', ParallelTesting::token());
        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
