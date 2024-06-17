<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use LaravelLang\Models\Casts\ColumnCast;
use LaravelLang\Models\Eloquent\Translation;

class TestModelTranslation extends Translation
{
    protected $fillable = [
        'locale',
        'title',
        'description',
    ];

    protected $casts = [
        'title'       => ColumnCast::class,
        'description' => ColumnCast::class,
    ];
}
