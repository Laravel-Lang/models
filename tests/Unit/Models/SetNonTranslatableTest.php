<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use Tests\Constants\FakeValue;

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel();

    $model->key = $key;
    $model->save();

    expect($model->key)->toBeString()->toBe($key);
});

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->setTranslation('foo', 'foo', FakeValue::LocaleUninstalled);
})->throws(AttributeIsNotTranslatableException::class);
