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
        if ($value) {
            return new ContentData(
                json_decode($value, true)
            );
        }

        return null;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  ContentData  $value
     * @param  array  $attributes
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value) {
            return $value->toJson(Config::shared()->models->flags);
        }

        return null;
    }
}
