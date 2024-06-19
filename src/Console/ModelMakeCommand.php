<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\ModelMakeCommand as BaseMakeCommand;
use Illuminate\Support\Str;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Services\ClassMap;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class ModelMakeCommand extends Command
{
    protected $signature = 'make:model:localization {model} {--columns=*}';

    protected $description = 'Creates a model for storing translations';

    protected array $columns = ['locale', 'title', 'description'];

    public function handle(): void
    {
        if (!$model = $this->model()) {
            info('You haven\'t selected a model.');

            return;
        }

        $columns = $this->columns();

        $this->generateModel($model, $columns, Config::shared()->models->suffix);
        $this->generateMigration($model, $columns, Config::shared()->models->suffix);
        $this->generateHelper($model);
    }

    protected function generateModel(string $model, array $columns, string $suffix): void
    {
        $fillable = array_map(
            fn (string $column) => sprintf("        '$column',"),
            $columns
        );

        $casts = array_map(
            fn (string $column) => sprintf("        '%s' => ColumnCast::class,", $column),
            array_filter($columns, fn (string $column) => $column !== 'locale')
        );

        $content = \DragonCode\Support\Facades\Helpers\Str::of(
            file_get_contents(__DIR__ . '/../../stubs/model.stub')
        )->replaceFormat([
            'namespace' => Str::of($model)->ltrim('\\')->beforeLast('\\'),
            'model' => Str::afterLast($model, '\\'),
            'suffix' => $suffix,
            'fillable' => implode(PHP_EOL, $fillable),
            'casts' => implode(PHP_EOL, $casts),
        ], '{{%s}}')
            ->toString();

        $path = ClassMap::path($model);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        File::store(
            Str::beforeLast($path, '.' . $extension) . $suffix . '.' . $extension,
            $content
        );
    }

    protected function generateMigration(string $model, array $columns, string $suffix): void
    {
        /** @var Model $base */
        $base = new $model;

        /** @var Translation $translated */
        $translated = new ($model . $suffix);

        $columns = array_map(
            fn (string $column) => sprintf("            \$table->string('$column')->nullable();"),
            array_filter($columns, fn (string $column) => $column !== 'locale')
        );

        $columnType = $base->getKeyType() === 'uuid' ? 'uuid' : 'bigInteger';

        $content = \DragonCode\Support\Facades\Helpers\Str::of(
            file_get_contents(__DIR__ . '/../../stubs/migration.stub')
        )->replaceFormat([
            'modelNamespace' => $model,
            'model' => class_basename($model),
            'table' => $translated->getTable(),
            'primaryType' => $columnType,
            'columns' => implode(PHP_EOL, $columns),
        ], '{{%s}}')
            ->toString();

        File::store(
            database_path(sprintf("migrations/%s_create_%s_table.php", date('Y_m_d_His'), $translated->getTable())),
            $content
        );
    }

    protected function generateHelper(string $model): void
    {
        $this->call(ModelsHelperCommand::class, compact('model'));
    }

    protected function model(): ?string
    {
        $model = $this->resolveModelClass(
            $this->askTranslationModel()
        );

        if (!$model) {
            if (!$this->ascToCreate()) {
                return null;
            }

            $this->createBaseModel($model);
        }

        return $model;
    }

    protected function columns(): array
    {
        if ($columns = $this->option('columns')) {
            return collect($columns)->prepend('locale')->all();
        }

        if ($columns = $this->askColumns()) {
            return collect($columns)->prepend('locale')->all();
        }

        return $this->columns;
    }

    protected function askTranslationModel(): string
    {
        if ($model = $this->argument('model')) {
            return $model;
        }

        return search(
            'Specify the model name for which you want to create a translation repository:',
            fn (string $value) => $this->findModel($value),
            'E.g. Post'
        );
    }

    protected function findModel(string $value): array
    {
        return ClassMap::find($value);
    }

    protected function askColumns(array $columns = []): ?array
    {
        if ($column = text('Enter a column name', hint: 'Or press Enter for continue')) {
            return array_filter(array_merge([$column], $this->askColumns($columns)));
        }

        return null;
    }

    protected function ascToCreate(): bool
    {
        return confirm('No model with this namespace was found. Do you want to create it?', true);
    }

    protected function resolveModelClass(string $model): ?string
    {
        $model = Str::of($model)->replace('/', '\\')->start('\\')->toString();

        $values = [
            $model,
            '\App' . $model,
            '\App\Models' . $model,
        ];

        foreach ($values as $value) {
            if (class_exists($value)) {
                return $value;
            }
        }

        return null;
    }

    protected function createBaseModel(string $model): void
    {
        $this->call(BaseMakeCommand::class, [
            'name' => Str::after($model, 'App\\Models\\'),
            '--migration' => true,
            '--factory' => true,
            '--seed' => true,
        ]);
    }
}
