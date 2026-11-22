<?php

declare(strict_types=1);

namespace LaravelLang\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Models\Concerns\HasNames;
use LaravelLang\Models\Concerns\HasScopes;
use LaravelLang\Models\Concerns\ModelLoader;
use LaravelLang\Models\Eloquent\Scopes\FilterTranslationsScope;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Services\AttributeService;
use LaravelLang\Models\Services\RelationService;
use LaravelLang\Models\Services\ValidatorService;
use LaravelLang\NativeLocaleNames\LocaleData;

use function in_array;
use function is_iterable;

/**
 * @property-read Collection<array-key, Translation> $translations
 *
 * @mixin HasAttributes
 * @mixin Model
 */
trait HasTranslations
{
    use HasNames;
    use HasScopes;
    use ModelLoader;

    /** @return HasMany<Translation, $this> */
    public function translations(): HasMany
    {
        $builder = $this->translationsRaw();

        $filter = new FilterTranslationsScope;

        $filter($builder);
        
        return $builder;
    }

    /** @return HasMany<Translation, $this> */
    public function translationsRaw(): HasMany
    {
        return $this->hasMany($this->translationClass(), 'item_id');
    }

    public function hasTranslated(string $column, Locale|LocaleData|string|null $locale = null): bool
    {
        return $this->translationAttribute($column, $locale)->has($column);
    }

    public function setTranslation(string $column, mixed $value, Locale|LocaleData|string|null $locale = null): Model
    {
        return $this->translationAttribute($column, $locale)->set($column, $value);
    }

    /** @param  iterable<Locale|LocaleData|string, mixed>  $values */
    public function setTranslations(string $column, iterable $values): static
    {
        foreach ($values as $locale => $value) {
            $this->setTranslation($column, $value, $locale);
        }

        return $this;
    }

    public function getTranslation(string $column, Locale|LocaleData|string|null $locale = null): mixed
    {
        return $this->translationAttribute($column, $locale)->get($column);
    }

    public function isTranslatable(string $column): bool
    {
        return in_array($column, $this->translatable(), true);
    }

    public function forgetTranslation(Locale|LocaleData|string $locale): Model
    {
        return (new AttributeService($this, $this->translation(...), $locale))
            ->validateLocale()
            ->forget();
    }

    public function forgetAllTranslations(): Model
    {
        return (new AttributeService($this, $this->translation(...)))->forgetAll($this->translationsRaw());
    }

    public function getAttribute($key): mixed
    {
        if ($this->isTranslatable($key)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value): Model
    {
        if ($this->isTranslatable($key)) {
            return $this->setTranslation($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function setRelation($relation, $value): static
    {
        $this->relations[$relation] = match ($relation) {
            'translations' => $value->keyBy('locale'),
            default        => $value
        };

        return $this;
    }

    public function fill(array $attributes): static
    {
        $basic        = Arr::except($attributes, $this->translatable());
        $translatable = Arr::only($attributes, $this->translatable());

        parent::fill($basic);

        foreach ($translatable as $key => $value) {
            is_iterable($value)
                ? $this->setTranslations($key, $value)
                : $this->setTranslation($key, $value);
        }

        return $this;
    }

    /** @return array<array-key, string> */
    public function translatable(): array
    {
        $class = $this->translationClass();

        return (new $class)->translatable();
    }

    public function translation(?string $locale): Translation
    {
        $normalized = ($locale ? Locales::fromAlias($locale) : null) ?? Locales::current(false);

        if (! $this->relationLoaded('translations')) {
            // On first access Eloquent returns the collection before setRelation() keys it by locale.
            $this->getRelationValue('translations');
        }

        return $this->translations->getOrPut(
            $normalized,
            fn (): Translation => RelationService::initializeLocale($this, $normalized, $this->translationsRaw()),
        );
    }

    protected function translationAttribute(string $column, Locale|LocaleData|string|null $locale): AttributeService
    {
        ValidatorService::column($column, $this->isTranslatable($column), $this->translatable(...));

        return (new AttributeService($this, $this->translation(...), $locale))->validateLocale();
    }
}
