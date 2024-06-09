<?php

declare(strict_types=1);

test('example', function () {
    getJson(route('welcome'))
        ->assertSuccessful();
});
