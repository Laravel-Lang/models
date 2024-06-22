<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\ParallelTesting;
use LaravelLang\Config\Enums\Name;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Constants\FakeValue;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) {
            $config->set('app.locale', FakeValue::LocaleMain);
            $config->set('app.fallback_locale', FakeValue::LocaleFallback);

            $config->set(Name::Hidden() . '.models.directory', [
                __DIR__ . '/../workbench/app/Models',
                base_path('app'),
            ]);

            $config->set('testing.parallel_token', ParallelTesting::token());
        });
    }
}
