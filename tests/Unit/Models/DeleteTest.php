<?php

declare(strict_types=1);

use App\Models\TestModel;
use App\Models\TestModelTranslation;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('soft delete', function () {
    $model = fakeModel(main: 'foo');

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );

    $model->delete();

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => now(),
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );
});

test('force delete', function () {
    $model = fakeModel(main: 'foo');

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );

    $model->forceDelete();

    assertDatabaseMissing(TestModel::class, [
        'id' => $model->id,
    ]);

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
    ]);
});

test('restore', function () {
    $model = fakeModel(main: 'foo');

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );

    $model->delete();

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => now(),
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );

    $model->restore();

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    Locales::installed()->each(
        fn (LocaleData $data) => assertDatabaseHas(TestModelTranslation::class, [
            'item_id' => $model->id,
            'locale'  => $data->code,
        ])
    );
});
