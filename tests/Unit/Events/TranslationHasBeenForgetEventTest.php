<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

beforeEach(function () {
    TestModel::create([
        'key' => fake()->word,

        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'qwerty 10',
            FakeValue::LocaleFallback => 'qwerty 11',
        ],

        FakeValue::ColumnDescription => [
            FakeValue::LocaleMain     => 'qwerty 20',
            FakeValue::LocaleFallback => 'qwerty 21',
        ],
    ]);

    Event::fake(TranslationHasBeenForgetEvent::class);
});

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

    Event::assertDispatched(function (TranslationHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === null;
    });
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

    Event::assertDispatched(function (TranslationHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === FakeValue::LocaleMain;
    });
});
