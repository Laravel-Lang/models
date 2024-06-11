<?php

declare(strict_types=1);

namespace LaravelLang\Models\Console;

use Illuminate\Console\Command;

class ModelsHelperCommand extends Command
{
    protected $signature = 'lang:models';

    protected $description = 'Command description';

    public function handle(): void {}
}
