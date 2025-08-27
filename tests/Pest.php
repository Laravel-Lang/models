<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelLang\Config\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Tests\Constants\FakeValue;

uses(Tests\TestCase::class, RefreshDatabase::class, WithWorkbench::class)
    ->compact()
    ->beforeEach(function () {
        Directory::ensureDirectory(database_path());

        Directory::ensureDirectory(lang_path(FakeValue::LocaleMain));
        Directory::ensureDirectory(lang_path(FakeValue::LocaleFallback));
        Directory::ensureDirectory(lang_path(FakeValue::LocaleCustom));

        Directory::ensureDelete(Config::shared()->models->helpers);

        File::ensureDelete(lang_path('en.json'));
    })
    ->afterAll(function () {
        Directory::ensureDelete(lang_path());
        Directory::ensureDelete(database_path());
    })->in(__DIR__);
