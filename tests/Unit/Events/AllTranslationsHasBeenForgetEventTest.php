<?php

declare(strict_types=1);

use App\Models\TestModel;
use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use Tests\Constants\FakeValue;

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

    Event::fake(AllTranslationsHasBeenForgetEvent::class);
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

    Event::assertDispatched(AllTranslationsHasBeenForgetEvent::class);
});
