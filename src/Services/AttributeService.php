<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;
use LaravelLang\Models\HasTranslations;
use LaravelLang\NativeLocaleNames\LocaleData;

use function filled;

/** @property Model|HasTranslations $model */
class AttributeService
{
    protected ?string $cachedLocale = null;

    protected ?string $cachedAlias = null;

    public function __construct(
        protected Model $model,
        protected ?string $column = null,
        protected Locale|LocaleData|string|null $locale = null,
    ) {}

    public function validate(): static
    {
        $this->validateColumn();
        $this->validateLocale();

        return $this;
    }

    public function validateColumn(): static
    {
        ValidatorService::column($this->model, $this->column);

        return $this;
    }

    public function validateLocale(): static
    {
        ValidatorService::locale($this->locale);

        return $this;
    }

    public function get(): mixed
    {
        if ($locale = $this->locale()) {
            return $this->model->translation($locale)->getAttribute($this->column);
        }

        return $this->model->translation($this->currentLocale())->getAttribute($this->column)
            ?? $this->model->translation($this->fallbackLocale())->getAttribute($this->column);
    }

    public function set(mixed $value): Model
    {
        $this->initRelation();
        $this->eventSetLocale($value);

        $this->model->translation($this->locale())->setAttribute($this->column, $value);

        return $this->model;
    }

    public function has(): bool
    {
        return filled($this->get());
    }

    public function forget(): Model
    {
        $locale = $this->locale();

        $this->model->translation($locale)->delete();
        $this->model->translations->forget($locale);

        $this->eventForget();

        return $this->model;
    }

    public function forgetAll(): Model
    {
        $this->model->translationsRaw()->delete();

        RelationService::clear($this->model);

        $this->eventForgetAll();

        return $this->model;
    }

    protected function initRelation(): void
    {
        RelationService::initialize($this->model);
    }

    protected function eventSetLocale(mixed $value): void
    {
        event(new TranslationHasBeenSetEvent($this->model, $this->column, $this->alias(), $this->get(), $value));
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
