<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use Tests\Constants\LocaleValue;

beforeEach(
    fn () => Event::fake(TranslationHasBeenSetEvent::class)
);

test('default locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === null
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});

test('main locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText, LocaleValue::LocaleMain);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === LocaleValue::LocaleMain
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText, LocaleValue::LocaleFallback);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === LocaleValue::LocaleFallback
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($oldText);

    // Change that
    $model->setTranslation(LocaleValue::ColumnTitle, $newText, LocaleValue::LocaleCustom);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(LocaleValue::ColumnTitle, LocaleValue::LocaleCustom))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === LocaleValue::ColumnTitle
            && $event->locale === LocaleValue::LocaleCustom
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});
