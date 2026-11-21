<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Eloquent\Translation;

final class FilterTranslationsScope
{
    /** @param Builder<Translation> $builder */
    public function __invoke(Builder $builder): void
    {
        if (! $this->enabled()) {
            return;
        }

        $builder->whereIn('locale', $this->locales());
    }

    protected function enabled(): bool
    {
        return app(ModelLangConfig::class)->filter;
    }

    /** @return array<array-key, string> */
    protected function locales(): array
    {
        return Locales::protects(false)->all();
    }
}
