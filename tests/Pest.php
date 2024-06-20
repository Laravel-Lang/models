<?php

use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class)->beforeEach(
    fn () => File::ensureDelete(lang_path('en.json'))
)->in(__DIR__);
