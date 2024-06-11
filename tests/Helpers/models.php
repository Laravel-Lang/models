<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

function fakeModel(string $key, ?string $text = null, ?string $fallback = null, ?string $custom = null): TestModel
{
    $model = TestModel::create(compact('key'));

    if ($text || $fallback) {
        fakeTranslation($model, $text, $fallback, $custom);
    }

    return $model;
}

function fakeTranslation(
    TestModel $model,
    ?string $text = null,
    ?string $fallback = null,
    ?string $custom = null
): Translation {
    $data = collect()
        ->when($text, fn (Collection $items) => $items->put(LocaleValue::LocaleMain, $text))
        ->when($fallback, fn (Collection $items) => $items->put(LocaleValue::LocaleFallback, $fallback))
        ->when($custom, fn (Collection $items) => $items->put(LocaleValue::LocaleCustom, $custom))
        ->all();

    return $model->translation()->create([
        'content' => new ContentData($data),
    ]);
}
