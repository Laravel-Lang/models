<?php

declare(strict_types=1);

use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

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
    $model->save();

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    // Check database
    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleMain => $oldText]),
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleMain => $newText]),
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
    $model->save();

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($newText);

    // Check database
    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleFallback => $oldText]),
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleFallback => $newText]),
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
    $model->save();

    expect($model->title)->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($newText);

    // Check database
    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleCustom => $oldText]),
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([FakeValue::LocaleCustom => $newText]),
    ]);
});

test('empties', function () {
    $model = fakeModel();

    $model->setTranslation(FakeValue::ColumnTitle, null, FakeValue::LocaleMain);
    $model->setTranslation(FakeValue::ColumnTitle, '', FakeValue::LocaleFallback);
    $model->setTranslation(FakeValue::ColumnTitle, ' ', FakeValue::LocaleCustom);

    $model->save();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => null,
    ]);
});

test('numeric', function () {
    $model = fakeModel();

    $model->setTranslation(FakeValue::ColumnTitle, 0, FakeValue::LocaleMain);
    $model->setTranslation(FakeValue::ColumnTitle, 0.01, FakeValue::LocaleFallback);

    $model->save();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe(0);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe(0.01);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncode([
            FakeValue::LocaleMain     => 0,
            FakeValue::LocaleFallback => 0.01,
        ]),
    ]);
});

test('unknown locale', function () {
    $model = fakeModel();

    $model->setTranslation(FakeValue::ColumnTitle, 'foo', FakeValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);
