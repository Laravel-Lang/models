<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use Illuminate\Foundation\Console\ModelMakeCommand as LaravelMakeModel;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Console\ModelMakeCommand as PackageMakeModel;

use function Pest\Laravel\artisan;

beforeEach(fn () => File::ensureDelete([
    base_path('app/Models/Test.php'),
    base_path('app/Models/TestTranslation.php'),
]));

afterEach(function () {
    $migrations = File::allPaths(database_path('migrations'), function (string $path) {
        return Path::extension($path) === 'php';
    });

    File::ensureDelete($migrations);

    File::ensureDelete([
        base_path('app/Models/Test.php'),
        base_path('app/Models/TestTranslation.php'),
    ]);
});

test('with exists model', function () {
    artisan(LaravelMakeModel::class, [
        'name' => 'Test',
    ])->run();

    artisan(PackageMakeModel::class, [
        'model'     => '\App\Models\Test',
        '--columns' => ['title', 'description'],
    ])->run();

    $model  = base_path('app/Models/TestTranslation.php');
    $helper = sprintf('%s/_ide_helper_models_%s.php', Config::shared()->models->helpers, md5('App\Models\Test'));

    $migrations = File::allPaths(database_path('migrations'), fn (string $path) => Path::extension($path) === 'php');

    expect($migrations)->toHaveCount(1);

    expect(file_get_contents($model))
        ->toContain('App\Models')
        ->toContain('class TestTranslation extends Translation')
        ->toContain(
            <<<'TEXT'
                    protected $fillable = [
                        'locale',
                        'title',
                        'description',
                    ];
                TEXT
        )
        ->toContain(
            <<<'TEXT'
                    protected $casts = [
                        'title' => TrimCast::class,
                        'description' => TrimCast::class,
                    ];
                TEXT
        );

    expect(file_get_contents($migrations[0]))
        ->toContain('Schema::create(\'test_translations\'')
        ->toContain('Schema::dropIfExists(\'test_translations\')')
        ->toContain('$table->string(\'title\')->nullable()')
        ->toContain('$table->string(\'description\')->nullable()')
        ->toContain('->constrained(\'tests\')');

    expect($helper)->toBeReadableFile();
});
