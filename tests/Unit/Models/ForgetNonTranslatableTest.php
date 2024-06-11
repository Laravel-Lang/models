<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\LocaleValue;

test('column', function () {
    $model = fakeModel();

    $model->forgetTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);

test('locale', function () {
    $model = fakeModel();

    $model->forgetTranslation(LocaleValue::ColumnTitle, 'foo');
})->throws(UnavailableLocaleException::class);
