<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseMissing;

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

    $model->forgetTranslation(LocaleValue::ColumnTitle);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();

    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');
});

test('locale', function () {
    $model = findFakeModel();

    $model->forgetTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');

    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');
});

test('all', function () {
    $model = findFakeModel();

    $model->forgetAllTranslations();

    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
    ]);

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();

    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBeNull();
});
