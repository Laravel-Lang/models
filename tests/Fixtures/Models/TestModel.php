<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelLang\Models\HasTranslations;

/**
 * @property string $key
 * @property string $title
 */
class TestModel extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'key',
        'title',
    ];

    protected function translatable(): array
    {
        return [
            'title',
        ];
    }
}
