<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelLang\Models\HasTranslations;

/**
 * @property string $key
 * @property string|null $title
 * @property string|null $description
 * @property Collection<TestModelTranslation> $translations
 * @property Collection<TestModelTranslation> $translationsRaw
 *
 * @method  HasMany translations
 * @method HasMany translationsRaw()
 */
class TestModel extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'key',
    ];
}
