<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelLang\Config\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Tests\Constants\FakeValue;

uses(Tests\TestCase::class, RefreshDatabase::class, WithWorkbench::class)
    ->compact()
    ->beforeEach(function () {
        $files = new Filesystem;

        $files->ensureDirectoryExists(database_path());

        $files->ensureDirectoryExists(lang_path(FakeValue::LocaleMain));
        $files->ensureDirectoryExists(lang_path(FakeValue::LocaleFallback));
        $files->ensureDirectoryExists(lang_path(FakeValue::LocaleCustom));

        $files->deleteDirectory(Config::shared()->models->helpers);

        $files->delete(lang_path('en.json'));
    })
    ->afterAll(function () {
        $files = new Filesystem;

        $files->deleteDirectory(lang_path());
        $files->deleteDirectory(database_path());
    })->in(__DIR__);
