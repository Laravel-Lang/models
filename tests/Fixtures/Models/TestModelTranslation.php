<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use LaravelLang\Models\Translation;

class TestModelTranslation extends Translation
{
    protected $fillable = [
        'title',
        'description',
    ];
}
