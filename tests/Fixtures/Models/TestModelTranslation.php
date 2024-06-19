<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use LaravelLang\Models\Eloquent\Translation;

class TestModelTranslation extends Translation
{
    protected $fillable = [
        'locale',
        'title',
        'description',
    ];
}
