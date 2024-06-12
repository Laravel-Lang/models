<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    expect($model->key)->toBeString()->toBe($key);
});

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->getTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);
