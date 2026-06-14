<?php

declare(strict_types=1);

use Tests\Constants\FakeValue;

dataset('locales-filter', [
    'enabled' => [
        'enabled' => true,
        'count'   => 2,

        'locales' => [
            FakeValue::LocaleFallback,
            FakeValue::LocaleMain,
        ],
    ],

    'disabled' => [
        'enabled' => false,
        'count'   => 3,

        'locales' => [
            FakeValue::LocaleCustom,
            FakeValue::LocaleFallback,
            FakeValue::LocaleMain,
        ],
    ],
]);
