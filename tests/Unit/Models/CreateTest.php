<?php

declare(strict_types=1);

use LaravelLang\Models\Data\ContentData;
use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('create one', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key'   => 'foo',
        'title' => 'bar',
    ]);

    expect($model->key)->toBe('foo');
    expect($model->title)->toBe('bar');

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([
            LocaleValue::LocaleMain => 'bar',
        ]),
    ]);
});

test('create many', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key'   => 'foo',
        'title' => [
            LocaleValue::LocaleMain        => 'qwerty 1',
            LocaleValue::LocaleFallback    => 'qwerty 2',
            LocaleValue::LocaleCustom      => 'qwerty 3',
            LocaleValue::LocaleUninstalled => 'qwerty 4',

            'some' => 'qwerty 5',
        ],
    ]);

    expect($model->key)->toBe('foo');
    expect($model->title)->toBe('qwerty 1');

    expect($model->getTranslation('title', LocaleValue::LocaleMain))->toBe('qwerty 1');
    expect($model->getTranslation('title', LocaleValue::LocaleFallback))->toBe('qwerty 2');
    expect($model->getTranslation('title', LocaleValue::LocaleCustom))->toBe('qwerty 3');
    expect($model->getTranslation('title', LocaleValue::LocaleUninstalled))->toBeNull();
    expect($model->getTranslation('title', 'some'))->toBeNull();

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([
            LocaleValue::LocaleMain     => 'qwerty 1',
            LocaleValue::LocaleFallback => 'qwerty 2',
            LocaleValue::LocaleCustom   => 'qwerty 3',
        ]),
    ]);
});

test('create data', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::create([
        'key'   => 'foo',
        'title' => new ContentData([
            LocaleValue::LocaleMain        => 'qwerty 1',
            LocaleValue::LocaleFallback    => 'qwerty 2',
            LocaleValue::LocaleCustom      => 'qwerty 3',
            LocaleValue::LocaleUninstalled => 'qwerty 4',

            'some' => 'qwerty 5',
        ]),
    ]);

    expect($model->key)->toBe('foo');
    expect($model->title)->toBe('qwerty 1');

    expect($model->getTranslation('title', LocaleValue::LocaleMain))->toBe('qwerty 1');
    expect($model->getTranslation('title', LocaleValue::LocaleFallback))->toBe('qwerty 2');
    expect($model->getTranslation('title', LocaleValue::LocaleCustom))->toBe('qwerty 3');
    expect($model->getTranslation('title', LocaleValue::LocaleUninstalled))->toBeNull();
    expect($model->getTranslation('title', 'some'))->toBeNull();

    assertDatabaseHas(TestModel::class, [
        'id'  => $model->id,
        'key' => $model->key,
    ]);

    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([
            LocaleValue::LocaleMain     => 'qwerty 1',
            LocaleValue::LocaleFallback => 'qwerty 2',
            LocaleValue::LocaleCustom   => 'qwerty 3',
        ]),
    ]);
});

test('make', function () {
    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);

    $model = TestModel::make([
        'key'   => 'foo',
        'title' => 'bar',
    ]);

    expect($model->key)->toBe('foo');
    expect($model->title)->toBe('bar');

    assertDatabaseEmpty(TestModel::class);
    assertDatabaseEmpty(Translation::class);
});
