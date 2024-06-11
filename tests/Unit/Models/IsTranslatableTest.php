<?php

declare(strict_types=1);

use Tests\Constants\LocaleValue;

test('attributes', function () {
    $model = fakeModel();

    expect($model->isTranslatable(LocaleValue::ColumnTitle))->toBeTrue();
    expect($model->isTranslatable(LocaleValue::ColumnDescription))->toBeTrue();

    expect($model->isTranslatable('key'))->toBeFalse();
    expect($model->isTranslatable('foo'))->toBeFalse();
});
