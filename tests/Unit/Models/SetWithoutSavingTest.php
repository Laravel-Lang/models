<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('main locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($newText);

    // Check database
    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleMain => $oldText]),
    ]);

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleMain => $newText]),
    ]);
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($newText);

    // Check database
    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleFallback => $oldText]),
    ]);

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleFallback => $newText]),
    ]);
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($newText);

    // Check database
    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleCustom => $oldText]),
    ]);

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([LocaleValue::LocaleCustom => $newText]),
    ]);
});

test('empties', function () {
    $model = fakeModel();

    $model->setTranslation(LocaleValue::ColumnTitle, null, LocaleValue::LocaleMain);
    $model->setTranslation(LocaleValue::ColumnTitle, '', LocaleValue::LocaleFallback);
    $model->setTranslation(LocaleValue::ColumnTitle, ' ', LocaleValue::LocaleCustom);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
    ]);
});

test('numeric', function () {
    $model = fakeModel();

    $model->setTranslation(LocaleValue::ColumnTitle, 0, LocaleValue::LocaleMain);
    $model->setTranslation(LocaleValue::ColumnTitle, 0.01, LocaleValue::LocaleFallback);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe(0);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe(0.01);

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
    ]);
});
