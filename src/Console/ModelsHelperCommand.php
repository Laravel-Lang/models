<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Instances\Instance;
use Illuminate\Console\Command;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\HasTranslations;
use LaravelLang\Models\Services\Autoloader;
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
        foreach ($this->classes() as $class) {
            if ($this->isTranslatable($class)) {
                $this->generate($class);
            }
        }
    }

    protected function generate(string $class): void
    {
        HelperGenerator::of($class)->generate();
    }

    protected function classes(): array
    {
        return Autoloader::classes();
    }

    protected function isTranslatable(string $class): bool
    {
        return Instance::of($class, HasTranslations::class);
    }

    protected function cleanUp(): void
    {
        Directory::ensureDelete(
            Config::shared()->models->helpers
        );
    }
}
