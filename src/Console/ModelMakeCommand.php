<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ModelMakeCommand as BaseMakeCommand;
use Illuminate\Support\Str;
use LaravelLang\Models\Generators\MigrationGenerator;
use LaravelLang\Models\Generators\ModelGenerator;
use LaravelLang\Models\Services\ClassMap;
use Symfony\Component\Console\Attribute\AsCommand;

use function array_filter;
use function array_merge;
use function class_exists;
use function compact;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:model:localization')]
class ModelMakeCommand extends Command
{
    protected $signature = 'make:model:localization {model} {--columns=*}';

    protected $description = 'Creates a model for storing translations';

    protected array $columns = ['locale', 'title', 'description'];

    public function handle(): void
    {
        if (! $model = $this->model()) {
            info('You haven\'t selected a model.');

            return;
        }

        $columns = $this->columns();

        $this->generateModel($model, $columns);
        $this->generateMigration($model, $columns);
        $this->generateHelper($model);
    }

    protected function generateModel(string $model, array $columns): void
    {
        ModelGenerator::of($model, $columns)->generate();
    }

    protected function generateMigration(string $model, array $columns): void
    {
        MigrationGenerator::of($model, $columns)->generate();
    }

    protected function generateHelper(string $model): void
    {
        $this->call(ModelsHelperCommand::class, compact('model'));
    }

    protected function model(): ?string
    {
        $name = $this->askTranslationModel();

        if ($model = $this->resolveModelClass($name)) {
            return $model;
        }

        $this->createBaseModel($name);

        return $name;
    }

    protected function columns(): array
    {
        if ($columns = $this->option('columns')) {
            return $columns;
        }

        if ($columns = $this->askColumns()) {
            return $columns;
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

    protected function resolveModelClass(string $model): ?string
    {
        $model = Str::of($model)
            ->replace('/', '\\')
            ->start('\\')
            ->toString();

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
            'name'        => Str::after($model, 'App\\Models\\'),
            '--migration' => true,
            '--factory'   => true,
            '--seed'      => true,
        ]);
    }
}
