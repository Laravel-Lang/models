<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

beforeEach(function () {
    TestModel::create([
        'key' => fake()->word,

        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain     => 'qwerty 10',
            LocaleValue::LocaleFallback => 'qwerty 11',
        ],

        LocaleValue::ColumnDescription => [
            LocaleValue::LocaleMain     => 'qwerty 20',
            LocaleValue::LocaleFallback => 'qwerty 21',
        ],
    ]);

    Event::fake(TranslationHasBeenForgetEvent::class);
});

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

    Event::assertDispatched(function (TranslationHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === null;
    });
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

    Event::assertDispatched(function (TranslationHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === LocaleValue::LocaleMain;
    });
});
