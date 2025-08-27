<?php

declare(strict_types=1);

use App\Models\TestModel;
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

test('translated scope returns records with at least one translation', function () {
    $text = fake()->paragraph;

    TestModel::create(['key' => FakeValue::LocaleFallback]);
    TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $text,
        ],
    ]);

    expect(TestModel::translated()->count())->toBe(1);
    expect(TestModel::translated()->first()->{FakeValue::ColumnTitle})->toBe($text);
});

test('whereTranslation filters by translation', function () {
    $text = 'Hello world';

    $model = TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $text,
        ],
    ]);

    expect(TestModel::whereTranslation(FakeValue::ColumnTitle, 'Hello')->count())->toBeEmpty();
    expect(TestModel::whereTranslation(FakeValue::ColumnTitle, 'Hello world')->first()->id)->toBe($model->id);
});

test('or whereTranslation filters by translation', function () {
    $model1 = TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => 'Texte en français',
        ],
    ]);
    $model2 = TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleFallback => 'Text auf Deutsch',
        ],
    ]);

    $result = TestModel::query()
        ->whereTranslation(FakeValue::ColumnTitle, 'Texte en français')
        ->orWhereTranslation(FakeValue::ColumnTitle, 'Text auf Deutsch')
        ->get();

    expect($result->count())->toBe(2);
    expect($result->first()->id)->toBe($model1->id);
    expect($result->last()->id)->toBe($model2->id);
});

test('where translation filters by translation and locale', function () {
    $text = 'Hello world';

    $model1 = TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $text,
        ],
    ]);
    TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleFallback => $text,
        ],
    ]);

    expect(TestModel::whereTranslation(FakeValue::ColumnTitle, $text)->count())->toBe(2);

    $result = TestModel::query()
        ->whereTranslation(FakeValue::ColumnTitle, $text, FakeValue::LocaleMain)
        ->get();

    expect($result->count())->toBe(1);
    expect($result->first()->id)->toBe($model1->id);
});

test('whereTranslationLike filters by translation', function () {
    $text = 'Hello world';

    TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $text,
        ],
    ]);

    expect(TestModel::whereTranslationLike(FakeValue::ColumnTitle, 'wor')->count())->toBe(0);
    expect(TestModel::whereTranslationLike(FakeValue::ColumnTitle, '%wor%')->count())->toBe(1);
});

test('or whereTranslationLike filters by translation', function () {
    $model1 = TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => 'Texte en français',
        ],
    ]);
    $model2 = TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleFallback => 'Text auf Deutsch',
        ],
    ]);

    $result = TestModel::query()
        ->whereTranslationLike(FakeValue::ColumnTitle, '%français')
        ->orWhereTranslationLike(FakeValue::ColumnTitle, '%Deutsch')
        ->get();

    expect($result->count())->toBe(2);
    expect($result->first()->id)->toBe($model1->id);
    expect($result->last()->id)->toBe($model2->id);
});

test('whereTranslationLike filters by translation and locale', function () {
    $text = 'Hello world';

    $model1 = TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain => $text,
        ],
    ]);
    TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleFallback => $text,
        ],
    ]);

    expect(TestModel::query()->whereTranslationLike(FakeValue::ColumnTitle, '%world%')->count())->toBe(2);

    $result = TestModel::query()->whereTranslationLike(FakeValue::ColumnTitle, '%world%', FakeValue::LocaleMain)->get();

    expect($result->count())->toBe(1);
    expect($result->first()->id)->toBe($model1->id);
});

test('orderByTranslation sorts by key asc', function () {
    TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'A',
            FakeValue::LocaleFallback => 'D',
        ],
    ]);
    TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'B',
            FakeValue::LocaleFallback => 'C',
        ],
    ]);

    expect(TestModel::query()->orderByTranslation(FakeValue::ColumnTitle, 'asc')->get()->first()->key)->toBe(FakeValue::LocaleMain);
    expect(TestModel::query()->orderByTranslation(FakeValue::ColumnTitle, 'asc', FakeValue::LocaleFallback)->get()->first()->key)->toBe(FakeValue::LocaleFallback);
});

test('orderByTranslation sorts by key desc', function () {
    TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'A',
            FakeValue::LocaleFallback => 'D',
        ],
    ]);
    TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'B',
            FakeValue::LocaleFallback => 'C',
        ],
    ]);

    expect(TestModel::query()->orderByTranslation(FakeValue::ColumnTitle, 'desc')->get()->first()->key)->toBe(FakeValue::LocaleFallback);
    expect(TestModel::query()->orderByTranslation(FakeValue::ColumnTitle, 'desc', FakeValue::LocaleFallback)->get()->first()->key)->toBe(FakeValue::LocaleMain);
});

test('orderByTranslation sorts by key asc even if locale is missing', function () {
    TestModel::create([
        'key'                  => FakeValue::LocaleMain,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'Pommes de Terre',
            FakeValue::LocaleFallback => 'Kartoffeln',
        ],
    ]);
    TestModel::create([
        'key'                  => FakeValue::LocaleFallback,
        FakeValue::ColumnTitle => [
            FakeValue::LocaleMain     => 'Fraises',
            FakeValue::LocaleFallback => 'Erdbeeren',
        ],
    ]);
    TestModel::create([
        'key' => FakeValue::LocaleCustom,
    ]);

    $orderInFrench = TestModel::orderByTranslation(FakeValue::ColumnTitle)->get();
    expect($orderInFrench->pluck(FakeValue::ColumnTitle)->toArray())->toBe([null, 'Fraises', 'Pommes de Terre']);

    app()->setLocale(FakeValue::LocaleFallback);
    $orderInDeutsch = TestModel::orderByTranslation(FakeValue::ColumnTitle, 'desc')->get();
    expect($orderInDeutsch->pluck(FakeValue::ColumnTitle)->toArray())->toBe(['Kartoffeln', 'Erdbeeren', null]);
});
