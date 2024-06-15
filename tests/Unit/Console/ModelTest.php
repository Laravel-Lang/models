<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use LaravelLang\Models\Console\ModelsHelperCommand;

use function Pest\Laravel\artisan;

beforeEach(fn () => Directory::ensureDelete([
    base_path('app/Models/Test.php'),
    base_path('app/Models/TestTranslation.php'),
]));

test('generation', function () {
    $path = base_path('app/Models/TestTranslation.php');

    expect($path)->not->toBeReadableFile();

    artisan(ModelsHelperCommand::class, [
        'name' => 'Test',
    ])->run();

    artisan(ModelGenerator::class, [
        'model'   => 'App\Models\Test',
        'columns' => ['test', 'description'],
    ])->run();

    expect($path)->toBeReadableFile();

    expect(file_get_contents($path))
        ->toContain('App\Models')
        ->toContain('TestTranslation')
        ->toContain('class TestTranslation extends Translation')
        ->toContain(
            <<<TEXT
    protected \$fillable = [
        'locale',
        'title',
        'description',
    ];
TEXT
        );
});
