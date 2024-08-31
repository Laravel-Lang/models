<?php

declare(strict_types=1);

use App\Models\TestModelTranslation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LaravelLang\Config\Enums\Name;
use LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException;
use LaravelLang\Models\Exceptions\UnavailableLocaleException;
use Tests\Constants\FakeValue;

use function Pest\Laravel\assertDatabaseEmpty;

test('main locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(main: $text);

    expect($model->title)->toBeString()->toBe($text);
    expect($model->description)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('fallback locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(fallback: $text);

    expect($model->title)->toBeString()->toBe($text);

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBe($text);
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('custom locale', function () {
    $text = fake()->paragraph;

    $model = fakeModel(custom: $text);

    expect($model->title)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBe($text);
});

test('uninstalled', function () {
    $model = fakeModel();

    $model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleUninstalled);
})->throws(UnavailableLocaleException::class);

test('without translations model', function () {
    $model = fakeModel();

    assertDatabaseEmpty(TestModelTranslation::class);

    expect($model->title)->toBeNull();

    expect($model->getTranslation(FakeValue::ColumnTitle))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleMain))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleFallback))->toBeNull();
    expect($model->getTranslation(FakeValue::ColumnTitle, FakeValue::LocaleCustom))->toBeNull();
});

test('lazy loading', function (bool $enabled, int $count, array $locales) {
    config()->set(Name::Shared() . '.models.filter.enabled', $enabled);

    $hasNonFilteredQuery = false;
    $hasFilteredQuery    = false;

    DB::listen(function (QueryExecuted $query) use (&$hasNonFilteredQuery, &$hasFilteredQuery) {
        if (Str::is('select * where *."item_id" = ? and *."item_id" is not null', $query->sql)) {
            $hasNonFilteredQuery = true;
        }

        if (Str::is('select * where *."item_id" = ? and *."item_id" is not null and "locale" in (?, ?)', $query->sql)) {
            $hasFilteredQuery = true;
        }
    });

    $model1 = fakeModel(main: 'Foo');
    $model2 = fakeModel(main: 'Bar');

    $model1->load('translations');
    $model2->load('translations');

    expect($model1->relationLoaded('translations'))->toBeTrue();
    expect($model2->relationLoaded('translations'))->toBeTrue();

    expect($model1->translations()->count())->toBe($count);
    expect($model2->translations()->count())->toBe($count);

    expect($model1->translations->count())->toBe($count);
    expect($model2->translations->count())->toBe($count);

    expect($model1->translations->pluck('locale')->sort()->values()->all())->toBe($locales);
    expect($model2->translations->pluck('locale')->sort()->values()->all())->toBe($locales);

    expect($hasNonFilteredQuery)->toBe(! $enabled);
    expect($hasFilteredQuery)->toBe($enabled);
})->with('locales-filter');

test('non-translatable attribute', function () {
    $key = fake()->word;

    $model = fakeModel($key);

    expect($model->key)->toBeString()->toBe($key);
});

test('not translatable attribute', function () {
    $model = fakeModel();

    $model->getTranslation('foo');
})->throws(AttributeIsNotTranslatableException::class);
