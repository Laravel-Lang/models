<?php

declare(strict_types=1);

use App\Models\TestModel;
use App\Models\TestModelTranslation;
use LaravelLang\Models\Services\ClassMap;

test('translatable', function () {
    expect(ClassMap::translatable())
        ->toContain(TestModel::class)
        ->not->toContain(TestModelTranslation::class);
});

test('available', function () {
    expect(ClassMap::available())
        ->toContain(TestModel::class)
        ->not->toContain(TestModelTranslation::class);
});

test('path', function () {
    expect(ClassMap::path('\\' . TestModel::class))->toEndWith('TestModel.php');
    expect(ClassMap::path('Unknown\\Model'))->toBeNull();
});
