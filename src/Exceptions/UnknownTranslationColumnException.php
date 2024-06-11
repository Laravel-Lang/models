<?php

declare(strict_types=1);

namespace LaravelLang\Models\Exceptions;

use Exception;

class UnknownTranslationColumnException extends Exception
{
    public function __construct(string $column)
    {
        parent::__construct("Unknown column: \"$column\"", 500);
    }
}
