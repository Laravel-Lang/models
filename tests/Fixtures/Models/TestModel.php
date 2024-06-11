<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\Models\HasTranslations;

class TestModel extends Model
{
    use HasTranslations;

    protected function translatable(): array
    {
        return [
            'title',
        ];
    }
}
