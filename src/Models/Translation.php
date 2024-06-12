<?php

declare(strict_types=1);

namespace LaravelLang\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelLang\Models\Casts\TranslationCast;
use LaravelLang\Models\Concerns\HasSetUp;
use LaravelLang\Models\Data\ContentData;

/**
 * @property ContentData $content
 */
class Translation extends Model
{
    use HasSetUp;
    use SoftDeletes;

    protected $fillable = [
        'content',
    ];

    // Backward compatibility for Laravel 10.
    protected $casts = [
        'model_type' => 'string',
        'model_id'   => 'string',

        'content' => TranslationCast::class,
    ];

    public function parent(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return $this->casts;
    }
}
