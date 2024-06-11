<?php

declare(strict_types=1);

use Tests\Fixtures\Models\TestModel;

test('get', function () {
    $model = TestModel::create();

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
    $model = TestModel::create();

    $locale   = config('app.locale');
    $fallback = config('app.fallback_locale');

    $text = fakeText();

    $model->title = $text;

    expect($model->title)->toBe($text);

    expect($model->translation->content->get('title', $locale))->toBe($text);
    expect($model->translation->content->get('title', $fallback))->toBe($text);

    expect($model->getTranslation('title'))->toBe($text);
    expect($model->getTranslation('title', $locale))->toBe($text);
    expect($model->getTranslation('title', $fallback))->toBe($text);
});
