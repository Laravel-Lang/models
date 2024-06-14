<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

beforeEach(
    fn () => TestModel::create([
        'key' => fake()->word,

        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain     => 'qwerty 10',
            LocaleValue::LocaleFallback => 'qwerty 11',
        ],

        LocaleValue::ColumnDescription => [
            LocaleValue::LocaleMain     => 'qwerty 20',
            LocaleValue::LocaleFallback => 'qwerty 21',
        ],
    ])
);

test('column', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetTranslation(LocaleValue::ColumnTitle);

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');
});

test('locale', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain);

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');
});

test('all', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetAllTranslations();

    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeFalse();

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeNull();

    expect($model->translation->content->getRaw())->toBeEmpty();
});

test('non-translatable column', function () {
    $model = fakeModel();

    $model->forgetTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);

test('non-translatable locale', function () {
    $model = fakeModel();

    $model->forgetTranslation(LocaleValue::ColumnTitle, 'foo');
})->throws(UnavailableLocaleException::class);
