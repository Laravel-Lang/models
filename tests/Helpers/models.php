<?php

declare(strict_types=1);

use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

function fakeModel(
    ?string $key = null,
    ?string $main = null,
    ?string $fallback = null,
    ?string $custom = null,
    ?string $uninstalled = null
): TestModel {
    $key ??= fake()->word;

    $model = TestModel::create(compact('key'));

    if ($main || $fallback || $custom || $uninstalled) {
        fakeTranslation($model, $main, $fallback, $custom);
    }

    return $model;
}

function fakeTranslation(
    TestModel $model,
    ?string $text = null,
    ?string $fallback = null,
    ?string $custom = null,
    ?string $uninstalled = null
): Translation {
    $data = [];

    if ($text) {
        $data[LocaleValue::ColumnTitle][LocaleValue::LocaleMain] = $text;
    }

    if ($fallback) {
        $data[LocaleValue::ColumnTitle][LocaleValue::LocaleFallback] = $fallback;
    }

    if ($custom) {
        $data[LocaleValue::ColumnTitle][LocaleValue::LocaleCustom] = $custom;
    }

    if ($uninstalled) {
        $data[LocaleValue::ColumnTitle][LocaleValue::LocaleUninstalled] = $uninstalled;
    }

    return $model->translation()->create([
        'content' => new ContentData($data),
    ]);
}

function findFakeModel(): TestModel
{
    return TestModel::firstOrFail();
}
