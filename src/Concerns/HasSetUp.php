<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Facades\Config;

trait HasSetUp
{
    public function initializeHasSetUp(): void
    {
        $config = Config::shared()->models;

        $this->connection = $config->connection;
        $this->table      = $config->table;
    }
}
