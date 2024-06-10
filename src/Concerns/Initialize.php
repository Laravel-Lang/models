<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Facades\Config;

trait Initialize
{
    protected function bootInitialize(): void
    {
        $config = Config::shared()->models;

        $this->connection = $config->connection;
        $this->table      = $config->table;

        $this->incrementing = $config->incrementing;
        $this->timestamps   = $config->timestamps;
    }
}
