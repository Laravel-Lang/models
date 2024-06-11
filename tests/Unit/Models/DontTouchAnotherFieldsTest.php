<?php

declare(strict_types=1);

test('get', function () {
    $key  = fake()->word;
    $text = fake()->paragraph;

    $model = fakeModel($key, $text);

    $model->setTranslation('title', fake()->paragraph);

    expect($model->key)->toBe($key);
});
