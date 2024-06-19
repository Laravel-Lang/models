<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class Translation extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return $this->casts;
    }
}
