<?php

declare(strict_types=1);

dataset('mixed-values', fn () => [
    'filled string'     => ['foo', 'foo'],
    'empty string'      => ['', null],
    'string with space' => [' ', null],

    'integer above zero' => [123, 123],
    'integer as zero'    => [0, 0],

    'float above zero' => [1.23, 1.23],
    'float as zero'    => [0.0, 0.0],

    'boolean true'  => [true, true],
    'boolean false' => [false, false],

    'null' => [null, null],
]);
