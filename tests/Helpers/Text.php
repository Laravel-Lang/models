<?php

declare(strict_types=1);

function fakeText(?string $locale = null): string
{
    return fake($locale)->words(5, true);
}
