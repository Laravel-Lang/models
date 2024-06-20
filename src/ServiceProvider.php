<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelLang\Models\Console\ModelMakeCommand;
use LaravelLang\Models\Console\ModelsHelperCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->bootCommands();
            $this->bootMigrations();
        }
    }

    protected function bootCommands(): void
    {
        $this->commands([
            ModelsHelperCommand::class,
            ModelMakeCommand::class,
        ]);
    }

    protected function bootMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations'
        );
    }
}
