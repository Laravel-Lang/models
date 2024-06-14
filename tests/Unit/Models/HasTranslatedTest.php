<?php

declare(strict_types=1);

use Tests\Constants\LocaleValue;

test('main', function () {
    $model = fakeModel(
        main: fake()->paragraph
    );

    expect($model->hasTranslated(LocaleValue::ColumnTitle))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeFalse();
});

test('fallback', function () {
    $model = fakeModel(
        fallback: fake()->paragraph
    );

    expect($model->hasTranslated(LocaleValue::ColumnTitle))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeTrue();
});

test('custom', function () {
    $model = fakeModel(
        custom: fake()->paragraph
    );

    expect($model->hasTranslated(LocaleValue::ColumnTitle))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBeTrue();
});
