<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\NativeLocaleNames\LocaleData;

use function filled;

class AttributeService
{
    protected ?string $cachedLocale = null;

    protected ?string $cachedAlias = null;

    /** @param Closure(?string): Translation $translate */
    public function __construct(
        protected Model $model,
        protected Closure $translate,
        protected Locale|LocaleData|string|null $locale = null,
    ) {}

    public function validateLocale(): static
    {
        ValidatorService::locale($this->locale);

        return $this;
    }

    public function get(string $column): mixed
    {
        if ($locale = $this->locale()) {
            return ($this->translate)($locale)->getAttribute($column);
        }

        return ($this->translate)($this->currentLocale())->getAttribute($column)
            ?? ($this->translate)($this->fallbackLocale())->getAttribute($column);
    }

    public function set(string $column, mixed $value): Model
    {
        $this->initRelation();
        $this->eventSetLocale($column, $value);

        ($this->translate)($this->locale())->setAttribute($column, $value);

        return $this->model;
    }

    public function has(string $column): bool
    {
        return filled($this->get($column));
    }

    public function forget(): Model
    {
        $locale = $this->locale();

        ($this->translate)($locale)->delete();
        $this->model->getRelationValue('translations')->forget($locale);

        $this->eventForget();

        return $this->model;
    }

    /** @param HasMany<Translation, *> $translations */
    public function forgetAll(HasMany $translations): Model
    {
        $translations->delete();

        RelationService::clear($this->model);

        $this->eventForgetAll();

        return $this->model;
    }

    protected function initRelation(): void
    {
        RelationService::initialize($this->model);
    }

    protected function eventSetLocale(string $column, mixed $value): void
    {
        event(new TranslationHasBeenSetEvent($this->model, $column, $this->alias(), $this->get($column), $value));
    }

    protected function eventForget(): void
    {
        event(new TranslationHasBeenForgetEvent($this->model, $this->alias()));
    }

    protected function eventForgetAll(): void
    {
        event(new AllTranslationsHasBeenForgetEvent($this->model));
    }

    protected function locale(): ?string
    {
        if (! $this->locale) {
            return null;
        }

        return $this->cachedLocale ??= Locales::fromAlias($this->locale);
    }

    protected function alias(): ?string
    {
        if (! $this->locale) {
            return null;
        }

        return $this->cachedAlias ??= Locales::toAlias($this->locale);
    }

    protected function currentLocale(): string
    {
        return Locales::current(false);
    }

    protected function fallbackLocale(): string
    {
        return Locales::fallback(false);
    }
}
