<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\File;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Console\ModelsHelperCommand;

use function Pest\Laravel\artisan;

test('console command', function () {
    $directory = Config::shared()->models->helpers;

    expect(File::allPaths($directory))->toBeEmpty();

    artisan(ModelsHelperCommand::class)->run();

    expect(File::allPaths($directory))->not->toBeEmpty();

    $file = File::allPaths($directory)[0];

    expect(file_get_contents($file))
        ->toContain('Tests\\Fixtures\\Models')
        ->toContain('TestModel')
        ->toContain('@property string $title');
});
