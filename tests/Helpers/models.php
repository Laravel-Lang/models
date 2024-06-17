<?php

declare(strict_types=1);

use LaravelLang\Models\Data\ContentData;
use Tests\Constants\FakeValue;
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
): void {
    $data = [];

    if ($text) {
        $data[FakeValue::ColumnTitle][FakeValue::LocaleMain] = $text;
    }

    if ($fallback) {
        $data[FakeValue::ColumnTitle][FakeValue::LocaleFallback] = $fallback;
    }

    if ($custom) {
        $data[FakeValue::ColumnTitle][FakeValue::LocaleCustom] = $custom;
    }

    if ($uninstalled) {
        $data[FakeValue::ColumnTitle][FakeValue::LocaleUninstalled] = $uninstalled;
    }

    $model->translation->fill([
        'content' => new ContentData($data),
    ])->save();
}

function findFakeModel(): TestModel
{
    return TestModel::firstOrFail();
}
