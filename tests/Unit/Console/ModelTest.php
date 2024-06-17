<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use Illuminate\Foundation\Console\ModelMakeCommand as LaravelMakeModel;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Console\ModelMakeCommand as PackageMakeModel;

use function Pest\Laravel\artisan;

beforeEach(fn () => Directory::ensureDelete([
    base_path('app/Models/Test.php'),
    base_path('app/Models/TestTranslation.php'),
]));

test('generation', function () {
    artisan(LaravelMakeModel::class, [
        'name' => 'Test',
    ])->run();

    artisan(PackageMakeModel::class, [
        'model' => 'App\Models\Test',
        'columns' => ['title', 'description'],
    ])->run();

    $model = base_path('app/Models/TestTranslation.php');
    $migration = database_path('migrations/' . date('Y_m_d_His') . '_create_test_translations_table.php');
    $helper = sprintf('%s/_ide_helper_models_%s.php', Config::shared()->models->helpers, md5('App\Models\Test'));

    expect(file_get_contents($model))
        ->toContain('App\Models')
        ->toContain('class TestTranslation extends Translation')
        ->toContain(
            <<<TEXT
    protected \$fillable = [
        'locale',
        'title',
        'description',
    ];
TEXT
        )
        ->toContain(
            <<<TEXT
    protected \$casts = [
        'title' => ColumnCast::class,
        'description' => ColumnCast::class,
    ];
TEXT

        );

    expect(file_get_contents($migration))
        ->toContain('Schema::create(\'test_translations\'')
        ->toContain('Schema::dropIfExists(\'test_translations\')')
        ->toContain('$table->bigInteger(\'item_id\')->index();')
        ->toContain('$table->json(\'title\')')
        ->toContain('$table->json(\'description\')');

    expect($helper)->toBeReadableFile();
});
