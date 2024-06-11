<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;

use function Pest\Laravel\assertDatabaseEmpty;

test('main locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(main: $text);

    expect($model->title)->toBeString()->toBe($text);

    expect($model->translation->content->get(LocaleValue::ColumnTitle))->toBe($text);
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($text);
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();

    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($text);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();
});

test('fallback locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(fallback: $text);

    expect($model->title)->toBeString()->toBe($text);

    expect($model->translation->content->get(LocaleValue::ColumnTitle))->toBe($text);
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($text);
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();

    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($text);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();
});

test('custom locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(custom: $text);

    expect($model->title)->toBeString()->toBeNull();

    expect($model->translation->content->get(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($text);

    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($text);
});

test('without translations model', function () {
    $text = fake()->paragraph;

    $model = fakeModel(custom: $text);

    assertDatabaseEmpty(Translation::class);

    expect($model->title)->toBeString()->toBeNull();

    expect($model->translation->content->get(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->translation->content->get(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();

    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeNull();
});
