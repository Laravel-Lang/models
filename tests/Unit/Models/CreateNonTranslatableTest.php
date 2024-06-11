<?php

declare(strict_types=1);

use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\LocaleValue;
use Tests\Fixtures\Models\TestModel;

test('array', function () {
    TestModel::create([
        'key' => 'foo',

        LocaleValue::ColumnTitle => [
            LocaleValue::LocaleMain        => 'qwerty 1',
            LocaleValue::LocaleUninstalled => 'qwerty 2',
        ],
    ]);
})->throws(UnavailableLocaleException::class);
