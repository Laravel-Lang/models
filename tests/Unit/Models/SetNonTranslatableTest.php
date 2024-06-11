<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\LocaleValue;

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel();

    $model->key = $key;
    $model->save();

    expect($model->key)->toBeString()->toBe($key);
});

test('unknown locale', function () {
    $model = fakeModel();

    $model->setTranslation(LocaleValue::ColumnTitle, 'foo', LocaleValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->setTranslation('foo', 'foo', LocaleValue::LocaleUninstalled);
})->throws(AttributeIsNotTranslatableException::class);
