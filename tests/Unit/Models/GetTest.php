<?php

declare(strict_types=1);

use App\Models\TestModelTranslation;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseEmpty;

test('main locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(main: $text);

    expect($model->title)->toBeString()->toBe($text);
    expect($model->description)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('fallback locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(fallback: $text);

    expect($model->title)->toBeString()->toBe($text);

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('custom locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(custom: $text);

    expect($model->title)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($text);
});

test('uninstalled', function () {
    $model = fakeModel();

    $model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);

test('without translations model', function () {
    $model = fakeModel();

    assertDatabaseEmpty(TestModelTranslation::class);

    expect($model->title)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    expect($model->key)->toBeString()->toBe($key);
});

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->getTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);
