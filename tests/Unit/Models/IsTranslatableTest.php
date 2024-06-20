<?php

declare(strict_types=1);

use Tests\Constants\FakeValue;

test('attributes', function () {
    $model = fakeModel();

    expect($model->isTranslatable(FakeValue::ColumnTitle))->toBeTrue();
    expect($model->isTranslatable(FakeValue::ColumnDescription))->toBeTrue();

    expect($model->isTranslatable('key'))->toBeFalse();
    expect($model->isTranslatable('foo'))->toBeFalse();
});
