<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
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

    Event::fake(AllTranslationsHasBeenForgetEvent::class);
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

    Event::assertDispatched(function (AllTranslationsHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey();
    });
});
