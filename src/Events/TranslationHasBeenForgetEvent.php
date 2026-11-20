<?php

declare(strict_types=1);

namespace LaravelLang\Models\Events;

use Illuminate\Database\Eloquent\Model;

final readonly class TranslationHasBeenForgetEvent
{
    public function __construct(
        public Model $model,
        public ?string $locale,
    ) {}
}
