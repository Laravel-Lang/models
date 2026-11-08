<?php

declare(strict_types=1);

use App\Models\TestModel;
use Tests\Constants\FakeValue;

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

function fakeTranslation(TestModel $model, ?string $text, ?string $fallback, ?string $custom): void
{
    $model->setTranslations(FakeValue::ColumnTitle, [
        FakeValue::LocaleMain     => $text,
        FakeValue::LocaleFallback => $fallback,
        FakeValue::LocaleCustom   => $custom,
    ])->save();
}

function findFakeModel(): TestModel
{
    return TestModel::firstOrFail();
}
