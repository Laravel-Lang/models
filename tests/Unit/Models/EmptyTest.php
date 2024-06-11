<?php

declare(strict_types=1);

test('get', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    expect($model->title)->toBeNull();

    expect($model->translation->content->get('title', $locale))->toBeNull();
    expect($model->translation->content->get('title', $fallback))->toBeNull();

    expect($model->getTranslation('title'))->toBeNull();
    expect($model->getTranslation('title', $locale))->toBeNull();
    expect($model->getTranslation('title', $fallback))->toBeNull();
});

test('set', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    $text = fake()->paragraph;

    $model->title = $text;

    expect($model->title)->toBe($text);

    expect($model->translation->content->get('title', $locale))->toBe($text);
    expect($model->translation->content->get('title', $fallback))->toBe($text);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($text);
    expect($model->getTranslation('title', $fallback))->toBe($text);
});
