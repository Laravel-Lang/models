<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Exceptions\InvalidModelCommandInputException;
use LaravelLang\Filesystem\Filesystem;
use LaravelLang\Models\Generators\MigrationGenerator;
use LaravelLang\Models\Generators\ModelGenerator;
use LaravelLang\Models\Generators\ParentGenerator;
use LaravelLang\Models\Services\ClassMapService;
use ReflectionClass;

use function array_filter;
use function array_merge;
use function class_exists;
use function is_a;
use function is_array;
use function is_string;
use function Laravel\Prompts\error;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

final class ModelMakeCommand extends Command
{
    protected $signature = 'make:model-translation {model?} {--columns=*}';

    protected $description = 'Creates a model for storing translations';

    /** @var list<string> */
    protected array $columns = ['title', 'description'];

    public function __invoke(Filesystem $filesystem): int
    {
        if (! $model = $this->model()) {
            error('A model class name is required.');

            return self::FAILURE;
        }

        if (! is_a($model, Model::class, true)) {
            error("The model at `$model` namespace was not found.");

            return self::FAILURE;
        }

        if (! (new ReflectionClass($model))->isInstantiable()) {
            error("The model at `$model` namespace cannot be instantiated.");

            return self::FAILURE;
        }

        if ($this->hasTranslation($model)) {
            warning('The specified model already has a translation repository.');

            return self::FAILURE;
        }

        $columns = $this->columns();

        $this->attachToParent($model, $filesystem);
        $this->generateModel($model, $columns, $filesystem);
        $this->generateMigration($model, $columns, $filesystem);
        $this->generateHelper($model);

        return self::SUCCESS;
    }

    /** @param  array<array-key, string>  $columns */
    protected function generateModel(string $model, array $columns, Filesystem $filesystem): void
    {
        (new ModelGenerator($model, $columns, $filesystem))->generate();
    }

    /** @param  array<array-key, string>  $columns */
    protected function generateMigration(string $model, array $columns, Filesystem $filesystem): void
    {
        (new MigrationGenerator($model, $columns, $filesystem))->generate();
    }

    protected function attachToParent(string $model, Filesystem $filesystem): void
    {
        (new ParentGenerator($model, [], $filesystem))->generate();
    }

    protected function generateHelper(string $model): void
    {
        $this->call(ModelsHelperCommand::class, ['model' => $model]);
    }

    protected function model(): ?string
    {
        if ($class = $this->askModel()) {
            return Str::start($class, '\\');
        }

        return null;
    }

    protected function askModel(): ?string
    {
        $model = $this->argument('model')
            ?: select(
                label  : 'Select a model to create a translation repository:',
                options: $this->models()
            );

        return is_string($model) ? $model : null;
    }

    protected function hasTranslation(string $model): bool
    {
        return class_exists($model . app(ModelLangConfig::class)->suffix);
    }

    /** @return array<array-key, string> */
    protected function columns(): array
    {
        if ($columns = $this->option('columns')) {
            if (! is_array($columns)) {
                throw InvalidModelCommandInputException::invalidColumns();
            }

            foreach ($columns as $column) {
                if (! is_string($column)) {
                    throw InvalidModelCommandInputException::invalidColumnName();
                }
            }

            return $columns;
        }

        if ($columns = $this->askColumns()) {
            return $columns;
        }

        return $this->columns;
    }

    /** @return array<class-string> */
    protected function models(): array
    {
        return ClassMapService::available();
    }

    /** @return array<int, string> */
    protected function askColumns(): array
    {
        if ($column = text('Enter a column name or press Enter for continue')) {
            return array_filter(array_merge([$column], $this->askColumns()));
        }

        return [];
    }
}
