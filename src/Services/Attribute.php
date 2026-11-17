<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use LaravelLang\Config\Services\AliasConfig;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Events\AllTranslationsHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenForgetEvent;
use LaravelLang\Models\Events\TranslationHasBeenSetEvent;

use function app;
use function filled;

/** @property EloquentModel|\LaravelLang\Models\HasTranslations $model */
class Attribute
{
    public function __construct(
        protected EloquentModel $model,
        protected ?string $column,
        protected Locale|string|null $locale,
        protected AliasConfig $aliasConfig,
    ) {}

    public static function of(
        EloquentModel $model,
        ?string $column = null,
        Locale|string|null $locale = null,
    ): static {
        return new static($model, $column, $locale, app(AliasConfig::class));
    }

    public function validate(): static
    {
        $this->validateColumn();
        $this->validateLocale();

        return $this;
    }

    public function validateColumn(): static
    {
        Validator::column($this->model, $this->column);

        return $this;
    }

    public function validateLocale(): static
    {
        Validator::locale($this->locale);

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

        Relation::clear($this->model);

        $this->eventForgetAll();

        return $this->model;
    }

    protected function initRelation(): void
    {
        Relation::initialize($this->model);
    }

    protected function eventSetLocale(mixed $value): void
    {
        TranslationHasBeenSetEvent::dispatch(
            $this->model,
            $this->column,
            $this->locale(),
            $this->get(),
            $value
        );
    }

    protected function eventForget(): void
    {
        TranslationHasBeenForgetEvent::dispatch($this->model, $this->locale());
    }

    protected function eventForgetAll(): void
    {
        AllTranslationsHasBeenForgetEvent::dispatch($this->model);
    }

    protected function locale(): ?string
    {
        return Locales::info($this->locale)->code;
    }

    protected function currentLocale(): string
    {
        return $this->aliasConfig->from(
            Locales::current()
        );
    }

    protected function fallbackLocale(): string
    {
        return $this->aliasConfig->from(
            Locales::fallback()
        );
    }
}
