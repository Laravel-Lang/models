<?php

declare(strict_types=1);

use App\Models\TestModelTranslation;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('main locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($oldText);

    // Change that
    $model->setTranslation(FakeValue::ColumnTitle, $newText);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    // Check database
    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($oldText);

    // Change that
    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleFallback);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($newText);

    // Check database
    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($oldText);

    // Change that
    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleCustom);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($newText);

    // Check database
    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('mixed symbols', function (mixed $source, mixed $saved) {
    $model = fakeModel();

    $model->setTranslation(FakeValue::ColumnTitle, $source, FakeValue::LocaleMain);

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($saved);

    assertDatabaseEmpty(TestModelTranslation::class);
})->with('mixed-values');
