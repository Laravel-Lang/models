<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use Tests\Constants\FakeValue;
use Tests\Fixtures\Models\TestModel;

beforeEach(fn () => TestModel::create([
    'key' => fake()->word,

    FakeValue::ColumnTitle => [
        FakeValue::LocaleMain     => 'qwerty 10',
        FakeValue::LocaleFallback => 'qwerty 11',
    ],

    FakeValue::ColumnDescription => [
        FakeValue::LocaleMain     => 'qwerty 20',
        FakeValue::LocaleFallback => 'qwerty 21',
    ],
]));

test('forget locale', function () {
    Event::fake(TranslationHasBeenForgetEvent::class);

    $model = findFakeModel();

    $model->forgetTranslation(FakeValue::LocaleMain);

    Event::assertDispatched(function (TranslationHasBeenForgetEvent $event) use ($model) {
        return $event->model->getKey() === $model->getKey()
            && $event->locale          === FakeValue::LocaleMain;
    });
});
