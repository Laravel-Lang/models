<?php

declare(strict_types=1);

namespace Tests;

use App\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\ParallelTesting;
use LaravelLang\Config\Enums\Name;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Constants\FakeValue;

use function base_path;
use function Orchestra\Testbench\workbench_path;

abstract class TestCase extends BaseTestCase
{
    protected function resolveApplication(): Application
    {
        return new Application(workbench_path());
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) {
            $config->set('app.locale', FakeValue::LocaleMain);
            $config->set('app.fallback_locale', FakeValue::LocaleFallback);

            $config->set(Name::Hidden() . '.models.directory', [
                __DIR__ . '/../workbench/app/Models',
                base_path('app'),
            ]);

            $config->set(
                Name::Shared() . '.models.helpers',
                __DIR__ . '/../workbench/vendor/_laravel_lang/' . $this->parallelToken()
            );
        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom([
            database_path(),
            __DIR__ . '/../workbench/database/migrations',
        ]);
    }

    protected function parallelToken(): string
    {
        return (string) ParallelTesting::token() ?: '0';
    }
}
