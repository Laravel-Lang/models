<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use App\Models\Test;
use DragonCode\Support\Facades\Instances\Instance;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ModelMakeCommand as BaseMakeCommand;
use Illuminate\Support\Str;

class ModelMakeCommand extends Command
{
    protected $signature = 'make:model:localization {model} {--columns=*}';

    protected $description = 'Creates a model for storing translations';

    public function handle(): void
    {
        if (! $model = $this->model()) {
            dd('nope', $model);
            return;
        }
        
        dd('aaa');
    }

    protected function model(): ?string
    {
        $model = $this->askTranslationModel();

        if (! $this->modelExists($model)) {
            if (! $this->askModel()) {
                return null;
            }

            $this->createBaseModel($model);
        }

        return $model;
    }

    protected function askTranslationModel(): string
    {
        if ($model = $this->argument('model')) {
            return $model;
        }

        return $this->ask('Specify the namespace of the model for which you want to create a storage');
    }

    protected function askModel(): bool
    {
        return $this->confirm('No model with this namespace was found. Do you want to create it?', true);
    }

    protected function modelExists(string $class): bool
    {
        dd(
          $class  ,
            Str::start($class, '\\'),
            class_exists(Str::start($class, '\\')),
            Test::class,
            class_exists(Test::class),
            Instance::exists(Test::class)
        );
        return class_exists(Str::start($class, '\\'));
    }

    protected function createBaseModel(string $model): void
    {
        $this->call(BaseMakeCommand::class, [
            'name'        => $model,
            '--migration' => true,
            '--factory'   => true,
            '--seed'      => true,
        ]);
    }
}
