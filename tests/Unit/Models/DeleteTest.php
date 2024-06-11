<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('soft delete', function () {
    $model = fakeModel(main: 'foo');

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'deleted_at' => null,
    ]);

    $model->delete();

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => now(),
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'deleted_at' => now(),
    ]);
});

test('force delete', function () {
    $model = fakeModel(main: 'foo');

    assertDatabaseHas(TestModel::class, [
        'id'         => $model->id,
        'deleted_at' => null,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'deleted_at' => null,
    ]);

    $model->forceDelete();

    assertDatabaseMissing(TestModel::class, [
        'id' => $model->id,
    ]);

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
    ]);
});
