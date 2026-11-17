<?php

declare(strict_types=1);

namespace LaravelLang\Models\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use LaravelLang\Config\Services\ModelConfig;
use LaravelLang\Locales\Facades\Locales;

use function array_unique;

class FilterTranslationsScope
{
    public function __invoke(Builder $builder): void
    {
        if (! $this->enabled()) {
            return;
        }

        $builder->whereIn('locale', $this->locales());
    }

    protected function enabled(): bool
    {
        return app(ModelConfig::class)->filter;
    }

    protected function locales(): array
    {
        return array_unique([
            Locales::fromAlias(Locales::current()),
            Locales::fromAlias(Locales::fallback()),
        ]);
    }
}
