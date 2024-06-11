<?php

declare(strict_types=1);

use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('single', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key' => 'foo',

        LocaleValue::ColumnTitle       => 'qwerty 10',
        LocaleValue::ColumnDescription => 'qwerty 20',
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
            LocaleValue::ColumnTitle       => [
                LocaleValue::LocaleMain => 'qwerty 10',
            ],
            LocaleValue::ColumnDescription => [
                LocaleValue::LocaleMain => 'qwerty 20',
            ],
        ]),
    ]);
});

test('array', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key' => 'foo',

        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain     => 'qwerty 10',
            LocaleValue::LocaleFallback => 'qwerty 11',
            LocaleValue::LocaleCustom   => 'qwerty 12',
        ],

        LocaleValue::ColumnDescription => [
            LocaleValue::LocaleMain     => 'qwerty 20',
            LocaleValue::LocaleFallback => 'qwerty 21',
            LocaleValue::LocaleCustom   => 'qwerty 22',
        ],
    ]);

    expect($model->key)->toBeString()->toBe('foo');

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe('qwerty 12');

    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 21');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleCustom))->toBe('qwerty 22');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
            LocaleValue::ColumnTitle => [
                LocaleValue::LocaleMain     => 'qwerty 10',
                LocaleValue::LocaleFallback => 'qwerty 11',
                LocaleValue::LocaleCustom   => 'qwerty 12',
            ],

            LocaleValue::ColumnDescription => [
                LocaleValue::LocaleMain     => 'qwerty 20',
                LocaleValue::LocaleFallback => 'qwerty 21',
                LocaleValue::LocaleCustom   => 'qwerty 22',
            ],
        ]),
    ]);
});

test('data object', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $data1 = new ContentData([
        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain     => 'qwerty 10',
            LocaleValue::LocaleFallback => 'qwerty 11',
            LocaleValue::LocaleCustom   => 'qwerty 12',
        ],

        LocaleValue::ColumnDescription => [
            LocaleValue::LocaleMain     => 'qwerty 20',
            LocaleValue::LocaleFallback => 'qwerty 21',
            LocaleValue::LocaleCustom   => 'qwerty 22',
        ],
    ]);

    $data2 = new ContentData([
        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain     => 'qwerty 30',
            LocaleValue::LocaleFallback => 'qwerty 31',
            LocaleValue::LocaleCustom   => 'qwerty 32',
        ],

        LocaleValue::ColumnDescription => [
            LocaleValue::LocaleMain     => 'qwerty 40',
            LocaleValue::LocaleFallback => 'qwerty 41',
            LocaleValue::LocaleCustom   => 'qwerty 42',
        ],
    ]);

    $model = TestModel::create([
        'key' => 'foo',

        LocaleValue::ColumnTitle       => $data1,
        LocaleValue::ColumnDescription => $data2,
    ]);

    expect($model->key)->toBeString()->toBe('foo');

    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe('qwerty 12');

    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleMain))->toBe('qwerty 40');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleFallback))->toBe('qwerty 41');
    expect($model->getTranslation(LocaleValue::ColumnDescription, LocaleValue::LocaleCustom))->toBe('qwerty 42');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,

        'content' => jsonEncodeRaw([
            LocaleValue::ColumnTitle => [
                LocaleValue::LocaleMain     => 'qwerty 10',
                LocaleValue::LocaleFallback => 'qwerty 11',
                LocaleValue::LocaleCustom   => 'qwerty 12',
            ],

            LocaleValue::ColumnDescription => [
                LocaleValue::LocaleMain     => 'qwerty 40',
                LocaleValue::LocaleFallback => 'qwerty 41',
                LocaleValue::LocaleCustom   => 'qwerty 42',
            ],
        ]),
    ]);
});
