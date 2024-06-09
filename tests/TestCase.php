<?php

namespace Tests;

use LaravelLang\Config\ServiceProvider as ConfigServiceProvider;
use LaravelLang\Locales\ServiceProvider as LocalesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Constants\LocaleValue;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LocalesServiceProvider::class,
            ConfigServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];

        $config->set('app.locale', LocaleValue::LocaleMain);
    }
}
