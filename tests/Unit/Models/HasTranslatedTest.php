<?php

declare(strict_types=1);

use Tests\Constants\FakeValue;

test('main', function () {
    $model = fakeModel(
        main: fake()->paragraph
    );

    expect($model->hasTranslated(FakeValue::ColumnTitle))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeFalse();
});

test('fallback', function () {
    $model = fakeModel(
        fallback: fake()->paragraph
    );

    expect($model->hasTranslated(FakeValue::ColumnTitle))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
});

test('custom', function () {
    $model = fakeModel(
        custom: fake()->paragraph
    );

    expect($model->hasTranslated(FakeValue::ColumnTitle))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeTrue();
});
