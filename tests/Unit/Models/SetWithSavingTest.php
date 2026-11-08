<?php

declare(strict_types=1);

use App\Models\TestModelTranslation;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('main locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($oldText);

    // That one
    $model->setTranslation(FakeValue::ColumnTitle, $newText);
    $model->save();

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    // Check database
    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($oldText);

    // That one
    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleFallback);
    $model->save();

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($newText);

    // Check database
    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    expect($model->{FakeValue::ColumnTitle})->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($oldText);

    // That one
    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleCustom);
    $model->save();

    expect($model->{FakeValue::ColumnTitle})->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($newText);

    // Check database
    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle => $oldText,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle => $newText,
    ]);
});

test('mixed symbols', function (mixed $source, mixed $saved) {
    $model = fakeModel();

    $model->setTranslation(FakeValue::ColumnTitle, $source, FakeValue::LocaleMain);
    $model->save();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($saved);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $saved,
    ]);
})->with('mixed-values');

test('array', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($oldText);

    // That one
    $model->update([
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $newText,
        ],
    ]);

    expect($model->{FakeValue::ColumnTitle})->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,

        FakeValue::ColumnTitle => $newText,
    ]);
});
