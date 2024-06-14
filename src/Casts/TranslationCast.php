<?php

declare(strict_types=1);

namespace LaravelLang\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Data\ContentData;

class TranslationCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ContentData
    {
        return new ContentData(
            $value ? json_decode($value, true) : []
        );
    }

    /**
     * @param  ContentData  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value) {
            return $value->toJson(Config::shared()->models->flags);
        }

        return null;
    }
}
