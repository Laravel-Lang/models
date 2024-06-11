<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\File;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Console\ModelsHelperCommand;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\artisan;

beforeEach(fn () => File::ensureDelete(
    Config::shared()->models->helpers
));

test('console command', function () {
    $directory = Config::shared()->models->helpers;

    $filename = '_ide_helper_models_' . md5(TestModel::class) . '.php';

    expect($directory . '/' . $filename)->not->toBeReadableFile();

    artisan(ModelsHelperCommand::class)->run();

    expect($directory . '/' . $filename)->toBeReadableFile();

    expect(file_get_contents($directory . '/' . $filename))
        ->toContain('Tests\\Fixtures\\Models')
        ->toContain('TestModel')
        ->toContain('@property string $title');
});
