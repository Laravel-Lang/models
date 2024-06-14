<?php

declare(strict_types=1);

return [
    'models' => [
        /** @deprecated */
        'connection' => env('LOCALIZATION_CONNECTION', env('DB_CONNECTION')),

        /** @deprecated */
        'table'      => 'translations',

        'suffix' => 'Translation',

        /** rename from `flags` */
        'json'   => JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE,

        'helpers' => base_path('vendor/_laravel_lang'),
    ],
];
