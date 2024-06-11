<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\LocaleValue;

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    expect($model->key)->toBeString()->toBe($key);
});

test('unknown locale', function () {
    $model = fakeModel();

    $model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->getTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);
