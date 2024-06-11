<?php

declare(strict_types=1);

use LaravelLang\Config\Facades\Config;

function jsonEncode(mixed $value): string
{
    return json_encode($value, Config::shared()->models->jsonFlags);
}
