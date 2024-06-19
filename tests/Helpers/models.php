<?php

declare(strict_types=1);

use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

function fakeModel(
    ?string $key = null,
    ?string $main = null,
    ?string $fallback = null,
    ?string $custom = null
): TestModel {
    $key ??= fake()->word;

    $model = TestModel::create(compact('key'));

    if ($main || $fallback || $custom) {
        fakeTranslation($model, $main, $fallback, $custom);
    }

    return $model;
}

function fakeTranslation(TestModel $model, ?string $text = null, ?string $fallback = null, ?string $custom = null): void
{
    $model->setTranslation(FakeValue::ColumnTitle, $text, FakeValue::LocaleMain);
    $model->setTranslation(FakeValue::ColumnTitle, $fallback, FakeValue::LocaleFallback);
    $model->setTranslation(FakeValue::ColumnTitle, $custom, FakeValue::LocaleCustom);
}

function findFakeModel(): TestModel
{
    return TestModel::firstOrFail();
}
