<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('get', function () {
    $key      = fake()->word;
    $text     = fake()->paragraph;
    $fallback = fake()->paragraph;
    $custom   = fake()->paragraph;

    $model = fakeModel($key, $text, $fallback, $custom);

    $locale = LocaleValue::LocaleCustom;

    expect($model->title)->toBe($text);
    expect($model->title)->not->toBe($custom);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($custom);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($custom);
});

test('set without saving', function () {
    $key       = fake()->word;
    $text      = fake()->paragraph;
    $fallback  = fake()->paragraph;
    $custom    = fake()->paragraph;
    $newCustom = fake()->paragraph;

    $model = fakeModel($key, $text, $fallback, $custom);

    $locale = LocaleValue::LocaleCustom;

    expect($model->title)->toBe($text);
    expect($model->title)->not->toBe($custom);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($custom);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($custom);

    // Change that
    $model->setTranslation('title', $newCustom, $locale);

    expect($model->title)->toBe($text);
    expect($model->title)->not->toBe($custom);
    expect($model->title)->not->toBe($newCustom);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->not->toBe($custom);
    expect($model->translation->content->get('title', $locale))->toBe($newCustom);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->not->toBe($custom);
    expect($model->getTranslation('title', $locale))->toBe($newCustom);

    // Check database
    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([
            LocaleValue::LocaleMain     => $text,
            LocaleValue::LocaleFallback => $fallback,
            LocaleValue::LocaleCustom   => $newCustom,
        ]),
    ]);
});

test('set with saving', function () {
    $key       = fake()->word;
    $text      = fake()->paragraph;
    $fallback  = fake()->paragraph;
    $custom    = fake()->paragraph;
    $newCustom = fake()->paragraph;

    $model = fakeModel($key, $text, $fallback, $custom);

    $locale = LocaleValue::LocaleCustom;

    expect($model->title)->toBe($text);
    expect($model->title)->not->toBe($custom);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($custom);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($custom);

    // Change that
    $model->setTranslation('title', $newCustom, $locale);
    $model->save();

    expect($model->title)->toBe($text);
    expect($model->title)->not->toBe($custom);
    expect($model->title)->not->toBe($newCustom);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->not->toBe($custom);
    expect($model->translation->content->get('title', $locale))->toBe($newCustom);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->not->toBe($custom);
    expect($model->getTranslation('title', $locale))->toBe($newCustom);

    // Check database
    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([
            LocaleValue::LocaleMain     => $text,
            LocaleValue::LocaleFallback => $fallback,
            LocaleValue::LocaleCustom   => $newCustom,
        ]),
    ]);
});
