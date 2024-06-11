<?php

declare(strict_types=1);

return [
    'models' => [
        'connection' => null,

        'table' => 'translations',

        'json_flags' => JSON_UNESCAPED_UNICODE ^ JSON_UNESCAPED_SLASHES,

        'helpers' => base_path('vendor/_laravel_lang'),
    ],

    'translators' => [
        'GoogleTranslate',
        'YandexTranslate',
        'DeeplTranslate',
    ],
];
