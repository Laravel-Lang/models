<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Support\ServiceProvider;
use LaravelLang\Models\Console\ModelMakeCommand;
use LaravelLang\Models\Console\ModelsHelperCommand;

class LangModelsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ModelsHelperCommand::class,
            ModelMakeCommand::class,
        ]);
    }
}
