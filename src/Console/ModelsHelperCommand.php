<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Filesystem\Filesystem;
use LaravelLang\Models\Generators\HelperGenerator;
use LaravelLang\Models\Services\ClassMapService;

use function is_string;
use function ltrim;

final class ModelsHelperCommand extends Command
{
    protected $signature = 'lang:models-helper {model?}';

    protected $description = 'Generating autocomplete translatable properties for models';

    public function __invoke(Filesystem $filesystem, ModelLangConfig $config): void
    {
        $this->cleanUp($filesystem, $config);
        $this->process($filesystem);
    }

    protected function process(Filesystem $filesystem): void
    {
        foreach ($this->models() as $model) {
            $this->components->task($model, fn () => $this->generate($model, $filesystem));
        }
    }

    protected function generate(string $model, Filesystem $filesystem): void
    {
        (new HelperGenerator($model, [], $filesystem))->generate();
    }

    /** @return array<int, string> */
    protected function models(): array
    {
        if ($model = $this->argument('model')) {
            if (! is_string($model)) {
                throw new InvalidArgumentException('The model argument must be a class name.');
            }

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
