<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelLang\Models\Console\ModelMakeCommand;
use LaravelLang\Models\Console\ModelsHelperCommand;

class LaravelLangModelsServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! $this->app->runningUnitTests()) {
            return;
        }

        $this->commands([
            ModelsHelperCommand::class,
            ModelMakeCommand::class,
        ]);
    }
}
