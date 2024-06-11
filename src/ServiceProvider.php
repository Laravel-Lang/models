<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelLang\Models\Console\ModelsHelperCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->commands([
                ModelsHelperCommand::class,
            ]);
        }
    }
}
