<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\HasTranslations;

/**
 * @property string $key
 * @property array|string|ContentData $title
 * @property array|string|ContentData $description
 */
class TestModel extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'key',
        //'title',
        //'description',
    ];

    /** @deprecated */
    public function translatable(): array
    {
        return [
            'title',
            'description',
        ];
    }
}
