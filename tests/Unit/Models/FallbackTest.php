<?php

declare(strict_types=1);

use LaravelLang\Models\Models\Translation;
use Tests\Fixtures\Models\TestModel;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('get', function () {
    $key  = fake()->word;
    $text = fake()->paragraph;

    $model = fakeModel($key, fallback: $text);

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    expect($model->title)->toBe($text);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($text);
    expect($model->translation->content->get('title', $fallback))->toBe($text);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($text);
    expect($model->getTranslation('title', $fallback))->toBe($text);
});

test('set without saving', function () {
    $key     = fake()->word;
    $text    = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel($key, fallback: $text);

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    expect($model->title)->toBe($text);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($text);
    expect($model->translation->content->get('title', $fallback))->toBe($text);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($text);
    expect($model->getTranslation('title', $fallback))->toBe($text);

    // Change that
    $model->setTranslation('title', $newText);

    expect($model->title)->toBe($newText);

    expect($model->translation->content->get('title'))->toBe($newText);
    expect($model->translation->content->get('title', $locale))->toBe($newText);
    expect($model->translation->content->get('title', $fallback))->toBe($newText);

    expect($model->getTranslation('title'))->toBe($newText);
    expect($model->getTranslation('title', $locale))->toBe($newText);
    expect($model->getTranslation('title', $fallback))->toBe($newText);

    // Check database
    assertDatabaseMissing(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([$locale => $newText]),
    ]);
});

test('set with saving', function () {
    $key     = fake()->word;
    $text    = fake()->paragraph;
    $newText = fake()->paragraph;

    $model = fakeModel($key, fallback: $text);

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    expect($model->title)->toBe($text);

    expect($model->translation->content->get('title'))->toBe($text);
    expect($model->translation->content->get('title', $locale))->toBe($text);
    expect($model->translation->content->get('title', $fallback))->toBe($text);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($text);
    expect($model->getTranslation('title', $fallback))->toBe($text);

    // Change that
    $model->setTranslation('title', $newText);
    $model->save();

    expect($model->title)->toBe($newText);

    expect($model->translation->content->get('title'))->toBe($newText);
    expect($model->translation->content->get('title', $locale))->toBe($newText);
    expect($model->translation->content->get('title', $fallback))->toBe($newText);

    expect($model->getTranslation('title'))->toBe($newText);
    expect($model->getTranslation('title', $locale))->toBe($newText);
    expect($model->getTranslation('title', $fallback))->toBe($newText);

    // Check database
    assertDatabaseHas(Translation::class, [
        'model_type' => TestModel::class,
        'model_id'   => $model->id,
        'content'    => jsonEncode([$locale => $newText]),
    ]);
});
