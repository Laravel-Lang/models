<?php

declare(strict_types=1);

namespace LaravelLang\Models\Events;

use Illuminate\Database\Eloquent\Model;

final readonly class TranslationHasBeenSetEvent
{
    public function __construct(
        public Model $model,
        public string $column,
        public ?string $locale,
        public mixed $oldValue,
        public mixed $newValue,
    ) {}
}
