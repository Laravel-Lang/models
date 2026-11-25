<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use function sprintf;
use function str_replace;

class TemplateService
{
    /** @param array<array-key, string> $data */
    public static function replaceFormat(string $template, array $data, string $format): string
    {
        foreach ($data as $key => $value) {
            $replace = sprintf($format, $key);

            $template = str_replace($replace, $value, $template);
        }

        return $template;
    }
}
