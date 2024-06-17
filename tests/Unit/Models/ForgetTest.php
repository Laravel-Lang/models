<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

beforeEach(
    fn () => TestModel::create([
        'key' => fake()->word,

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'qwerty 10',
            FakeValue::LocaleFallback => 'qwerty 11',
        ],

        FakeValue::ColumnDescription => [
            FakeValue::LocaleMain     => 'qwerty 20',
            FakeValue::LocaleFallback => 'qwerty 21',
        ],
    ])
);

test('column', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetTranslation(FakeValue::ColumnTitle);

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');
});

test('locale', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain);

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');
});

test('all', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');

    $model->forgetAllTranslations();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeFalse();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeNull();

    expect($model->translation->content->getRaw())->toBeEmpty();
});

test('non-translatable column', function () {
    $model = fakeModel();

    $model->forgetTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);

test('non-translatable locale', function () {
    $model = fakeModel();

    $model->forgetTranslation(FakeValue::ColumnTitle, 'foo');
})->throws(UnavailableLocaleException::class);
