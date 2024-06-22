<?php

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Constants\FakeValue;

uses(Tests\TestCase::class, RefreshDatabase::class)->beforeEach(function () {
    Directory::ensureDirectory(lang_path(FakeValue::LocaleMain));
    Directory::ensureDirectory(lang_path(FakeValue::LocaleFallback));
    Directory::ensureDirectory(lang_path(FakeValue::LocaleCustom));

    File::ensureDelete(lang_path('en.json'));
})->in(__DIR__);
