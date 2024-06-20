<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class Translation extends Model
{
    public $timestamps = false;

    public function translatable(): array
    {
        return array_filter($this->getFillable(), function (string $column) {
            return $column !== 'locale';
        });
    }

    protected function casts(): array
    {
        return $this->casts;
    }
}
