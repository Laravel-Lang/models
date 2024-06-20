<?php

declare(strict_types=1);

namespace Tests\Concerns;

use DragonCode\Support\Facades\Filesystem\Directory;
use Tests\Constants\FakeValue;

trait Locales
{
    public function setUpLocales(): void
    {
        Directory::ensureDirectory(lang_path(FakeValue::LocaleMain));
        Directory::ensureDirectory(lang_path(FakeValue::LocaleFallback));
        Directory::ensureDirectory(lang_path(FakeValue::LocaleCustom));
    }
}
