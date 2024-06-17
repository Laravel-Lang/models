<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use Tests\Constants\FakeValue;

beforeEach(
    fn () => Event::fake(TranslationHasBeenSetEvent::class)
);

test('default locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(main: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === FakeValue::ColumnTitle
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
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleMain);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === FakeValue::ColumnTitle
            && $event->locale === FakeValue::LocaleMain
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});

test('fallback locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(fallback: $oldText);

    expect($model->title)->toBeString()->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($oldText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleFallback);

    expect($model->title)->toBeString()->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($newText);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === FakeValue::ColumnTitle
            && $event->locale === FakeValue::LocaleFallback
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});

test('custom locale', function () {
    $oldText = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel(custom: $oldText);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($oldText);

    $model->setTranslation(FakeValue::ColumnTitle, $newText, FakeValue::LocaleCustom);

    expect($model->title)->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($newText);

    Event::assertDispatched(function (TranslationHasBeenSetEvent $event) use ($model, $oldText, $newText) {
        return $event->model->getKey() === $model->getKey()
            && $event->column === FakeValue::ColumnTitle
            && $event->locale === FakeValue::LocaleCustom
            && $event->oldValue === $oldText
            && $event->newValue === $newText;
    });
});
