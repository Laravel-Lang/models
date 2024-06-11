<?php

declare(strict_types=1);

use LaravelLang\Config\Facades\Config;
use Tests\Constants\LocaleValue;

function jsonEncode(array $value): string
{
    return jsonEncodeRaw([LocaleValue::ColumnTitle => $value]);
}

function jsonEncodeRaw(array $value): string
{
    return json_encode($value, Config::shared()->models->jsonFlags);
}
