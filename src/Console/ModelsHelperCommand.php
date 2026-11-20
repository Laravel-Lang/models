<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use Illuminate\Console\Command;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Filesystem\Filesystem;
use LaravelLang\Models\Generators\HelperGenerator;
use LaravelLang\Models\Services\ClassMapService;

use function ltrim;

final class ModelsHelperCommand extends Command
{
    protected $signature = 'lang:models-helper {model?}';

    protected $description = 'Generating autocomplete translatable properties for models';

    public function __invoke(Filesystem $filesystem, ModelLangConfig $config): void
    {
        $this->cleanUp($filesystem, $config);
        $this->process();
    }

    protected function process(): void
    {
        foreach ($this->models() as $model) {
            $this->components->task($model, fn () => $this->generate($model));
        }
    }

    protected function generate(string $model): void
    {
        HelperGenerator::of($model)->generate();
    }

    protected function models(): array
    {
        if ($model = $this->argument('model')) {
            return [ltrim($model, '\\')];
        }

        return ClassMapService::translatable();
    }

    protected function cleanUp(Filesystem $filesystem, ModelLangConfig $config): void
    {
        $filesystem->ensureDelete(
            $config->helpers
        );
    }
}
