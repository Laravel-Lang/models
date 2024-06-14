<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use Tests\Constants\LocaleValue;

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel();

    $model->key = $key;
    $model->save();

    expect($model->key)->toBeString()->toBe($key);
});

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->setTranslation('foo', 'foo', LocaleValue::LocaleUninstalled);
})->throws(AttributeIsNotTranslatableException::class);
