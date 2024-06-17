<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
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
    $model->translation->fill([
        FakeValue::ColumnTitle => collect()
            ->when($text, fn (Collection $values) => $values->put(FakeValue::LocaleMain, $text))
            ->when($fallback, fn (Collection $values) => $values->put(FakeValue::LocaleFallback, $fallback))
            ->when($custom, fn (Collection $values) => $values->put(FakeValue::LocaleCustom, $custom))
            ->when($uninstalled, fn (Collection $values) => $values->put(FakeValue::LocaleUninstalled, $uninstalled))
            ->all()
    ])->save();
}

function findFakeModel(): TestModel
{
    return TestModel::firstOrFail();
}
