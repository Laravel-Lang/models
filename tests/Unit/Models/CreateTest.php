<?php

declare(strict_types=1);

use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('single', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

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

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
            FakeValue::ColumnTitle => [
                FakeValue::LocaleMain => 'qwerty 10',
            ],
            FakeValue::ColumnDescription => [
                FakeValue::LocaleMain => 'qwerty 20',
            ],
        ]),
    ]);
});

test('array', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

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

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe('qwerty 12');

    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBe('qwerty 22');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
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
        ]),
    ]);
});

test('data object', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $data1 = new ContentData([
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

    $data2 = new ContentData([
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'qwerty 30',
            FakeValue::LocaleFallback => 'qwerty 31',
            FakeValue::LocaleCustom   => 'qwerty 32',
        ],

        FakeValue::ColumnDescription => [
            FakeValue::LocaleMain     => 'qwerty 40',
            FakeValue::LocaleFallback => 'qwerty 41',
            FakeValue::LocaleCustom   => 'qwerty 42',
        ],
    ]);

    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle       => $data1,
        FakeValue::ColumnDescription => $data2,
    ]);

    expect($model->key)->toBeString()->toBe('foo');

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe('qwerty 12');

    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 40');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 41');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBe('qwerty 42');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
            FakeValue::ColumnTitle => [
                FakeValue::LocaleMain     => 'qwerty 10',
                FakeValue::LocaleFallback => 'qwerty 11',
                FakeValue::LocaleCustom   => 'qwerty 12',
            ],

            FakeValue::ColumnDescription => [
                FakeValue::LocaleMain     => 'qwerty 40',
                FakeValue::LocaleFallback => 'qwerty 41',
                FakeValue::LocaleCustom   => 'qwerty 42',
            ],
        ]),
    ]);
});

test('uninstalled store', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain        => 'qwerty 10',
            FakeValue::LocaleUninstalled => 'qwerty 11',
        ],
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
            FakeValue::ColumnTitle => [
                FakeValue::LocaleMain        => 'qwerty 10',
                FakeValue::LocaleUninstalled => 'qwerty 11',
            ],
        ]),
    ]);
});

test('uninstalled reading', function () {
    $model = TestModel::create([
        'key' => 'foo',

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain        => 'qwerty 10',
            FakeValue::LocaleUninstalled => 'qwerty 11',
        ],
    ]);

    $model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);
