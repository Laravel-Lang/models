<?php

declare(strict_types=1);

use App\Models\TestModel;
use App\Models\TestModelTranslation;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('single', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(TestModelTranslation::class);

    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle       => 'qwerty 10',
        FakeValue::ColumnDescription => 'qwerty 20',
    ]);

    expect($model->key)->toBeString()->toBe('foo');
    expect($model->title)->toBeString()->toBe('qwerty 10');
    expect($model->description)->toBeString()->toBe('qwerty 20');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => Locale::French,

        FakeValue::ColumnTitle       => 'qwerty 10',
        FakeValue::ColumnDescription => 'qwerty 20',
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => Locale::German,

        FakeValue::ColumnTitle       => null,
        FakeValue::ColumnDescription => null,
    ]);

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => Locale::Assamese,
    ]);
});

test('array', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(TestModelTranslation::class);

    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'qwerty 10',
            FakeValue::LocaleFallback => 'qwerty 11',
            FakeValue::LocaleCustom   => 'qwerty 12',
        ],

        FakeValue::ColumnDescription => [
            FakeValue::LocaleMain     => 'qwerty 20',
            FakeValue::LocaleFallback => 'qwerty 21',
            FakeValue::LocaleCustom   => 'qwerty 22',
        ],
    ]);

    expect($model->key)->toBeString()->toBe('foo');
    expect($model->title)->toBeString()->toBe('qwerty 10');
    expect($model->description)->toBeString()->toBe('qwerty 20');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleMain,

        FakeValue::ColumnTitle       => 'qwerty 10',
        FakeValue::ColumnDescription => 'qwerty 20',
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle       => 'qwerty 11',
        FakeValue::ColumnDescription => 'qwerty 21',
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle       => 'qwerty 12',
        FakeValue::ColumnDescription => 'qwerty 22',
    ]);
});

test('collection', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(TestModelTranslation::class);

    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => collect([
            FakeValue::LocaleMain     => 'qwerty 10',
            FakeValue::LocaleFallback => 'qwerty 11',
            FakeValue::LocaleCustom   => 'qwerty 12',
        ]),

        FakeValue::ColumnDescription => collect([
            FakeValue::LocaleMain     => 'qwerty 20',
            FakeValue::LocaleFallback => 'qwerty 21',
            FakeValue::LocaleCustom   => 'qwerty 22',
        ]),
    ]);

    expect($model->key)->toBeString()->toBe('foo');
    expect($model->title)->toBeString()->toBe('qwerty 10');
    expect($model->description)->toBeString()->toBe('qwerty 20');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleMain,

        FakeValue::ColumnTitle       => 'qwerty 10',
        FakeValue::ColumnDescription => 'qwerty 20',
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleFallback,

        FakeValue::ColumnTitle       => 'qwerty 11',
        FakeValue::ColumnDescription => 'qwerty 21',
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,

        'locale' => FakeValue::LocaleCustom,

        FakeValue::ColumnTitle       => 'qwerty 12',
        FakeValue::ColumnDescription => 'qwerty 22',
    ]);
});

test('uninstalled', function () {
    TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain        => 'qwerty 10',
            FakeValue::LocaleUninstalled => 'qwerty 11',
        ],

        FakeValue::ColumnDescription => [
            FakeValue::LocaleMain        => 'qwerty 20',
            FakeValue::LocaleUninstalled => 'qwerty 21',
        ],
    ]);
})->throws(UnavailableLocaleException::class);

test('unknown', function () {
    TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => [
            'qwerty' => 'qwerty 10',
        ],

        FakeValue::ColumnDescription => [
            'qwerty' => 'qwerty 20',
        ],
    ]);
})->throws(UnavailableLocaleException::class);
