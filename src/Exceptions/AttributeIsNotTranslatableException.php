<?php

declare(strict_types=1);

namespace LaravelLang\Models\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class AttributeIsNotTranslatableException extends Exception
{
    /**
     * @param  string  $column
     * @param  \Illuminate\Database\Eloquent\Model|\LaravelLang\Models\HasTranslations  $model
     */
    public function __construct(string $column, Model $model)
    {
        $available = implode(', ', $model->translatable());

        parent::__construct(
            "Cannot translate attribute `$column` as it's not on of the translatable attributes: `$available`.",
            500
        );
    }
}
