<?php

declare(strict_types=1);

namespace Tests\Concerns;

use DragonCode\Support\Facades\Filesystem\Directory;
use Tests\Constants\LocaleValue;

trait Locales
{
    public function setUpLocales(): void
    {
        Directory::ensureDirectory(lang_path(LocaleValue::LocaleMain));
        Directory::ensureDirectory(lang_path(LocaleValue::LocaleFallback));
        Directory::ensureDirectory(lang_path(LocaleValue::LocaleCustom));
    }
}
