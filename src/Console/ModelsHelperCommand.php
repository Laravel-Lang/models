<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use DragonCode\Support\Facades\Filesystem\Directory;
use Illuminate\Console\Command;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Services\ClassMap;
use LaravelLang\Models\Services\HelperGenerator;

class ModelsHelperCommand extends Command
{
    protected $signature = 'lang:models';

    protected $description = 'Generating autocomplete translatable properties for models';

    public function handle(): void
    {
        $this->cleanUp();
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
        return ClassMap::get();
    }

    protected function cleanUp(): void
    {
        Directory::ensureDelete(
            Config::shared()->models->helpers
        );
    }
}
