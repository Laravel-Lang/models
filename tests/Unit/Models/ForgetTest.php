<?php

declare(strict_types=1);

use App\Models\TestModel;
use App\Models\TestModelTranslation;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(
    fn () => TestModel::create([
        'key' => fake()->word,

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
    ])
);

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

    $model->forgetTranslation(FakeValue::LocaleMain);

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleMain,
    ]);

    assertDatabaseHas(TestModelTranslation::class, [
        'item_id' => $model->id,
        'locale'  => FakeValue::LocaleFallback,
    ]);
});

test('all', function () {
    $model = findFakeModel();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeTrue();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBeTrue();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe('qwerty 10');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe('qwerty 11');
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe('qwerty 12');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBe('qwerty 20');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBe('qwerty 21');
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBe('qwerty 22');

    $model->forgetAllTranslations();

    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeFalse();
    expect($model->hasTranslated(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBeFalse();

    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnDescription, FakeValue::LocaleCustom))->toBeNull();

    assertDatabaseMissing(TestModelTranslation::class, [
        'item_id' => $model->id,
    ]);
});

test('non-translatable locale', function () {
    $model = fakeModel();

    $model->forgetTranslation(FakeValue::ColumnTitle, 'foo');
})->throws(UnavailableLocaleException::class);
