<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Console\ModelsHelperCommand;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\artisan;

beforeEach(fn () => Directory::ensureDelete(
    Config::shared()->models->helpers
));

test('generate many models', function () {
    $path = sprintf(
        '%s/_ide_helper_models_%s.php',
        Config::shared()->models->helpers,
        md5(TestModel::class)
    );

    expect($path)->not->toBeReadableFile();

    artisan(ModelsHelperCommand::class)->run();

    expect($path)->toBeReadableFile();

    expect(file_get_contents($path))
        ->toContain('namespace Tests\Fixtures\Models {')
        ->toContain('class TestModel extends Model {}')
        ->toContain('@property array|string|null $title')
        ->toContain('@property array|string|null $description');
});

test('generate one model', function () {
    $path = sprintf(
        '%s/_ide_helper_models_%s.php',
        Config::shared()->models->helpers,
        md5(TestModel::class)
    );

    expect($path)->not->toBeReadableFile();

    artisan(ModelsHelperCommand::class, [
        'model' => TestModel::class,
    ])->run();

    expect($path)->toBeReadableFile();

    expect(file_get_contents($path))
        ->toContain('namespace Tests\Fixtures\Models {')
        ->toContain('class TestModel extends Model {}')
        ->toContain('@property array|string|null $title')
        ->toContain('@property array|string|null $description');
});
