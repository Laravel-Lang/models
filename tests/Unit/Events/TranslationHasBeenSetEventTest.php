<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use Tests\Constants\FakeValue;

beforeEach(
    fn () => Event::fake(TranslationHasBeenSetEvent::class)
);

test('default locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === null
            && $event->oldValue        === $oldText
            && $event->newValue        === $newText;
    });
});

test('main locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleMain);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === Locale::French
            && $event->oldValue        === $oldText
            && $event->newValue        === $newText;
    });
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleFallback);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === Locale::German
            && $event->oldValue        === $oldText
            && $event->newValue        === $newText;
    });
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleCustom);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column          === FakeValue::ColumnTitle
            && $event->locale          === Locale::Assamese
            && $event->oldValue        === $oldText
            && $event->newValue        === $newText;
    });
});
