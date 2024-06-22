<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->lazyLoading();
    }

    protected function lazyLoading(): void
    {
        Model::preventLazyLoading();
    }
}
