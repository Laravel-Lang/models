<?php

declare(strict_types=1);

namespace Tests\Fixtures\Models;

use App\Models\TestTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelLang\Models\HasTranslations;

/**
 * @property string $key
 * @property string|null $title
 * @property string|null $description
 * @property-read TestTranslation $translation
 */
class TestModel extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'key',
    ];
}
