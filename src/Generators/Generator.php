<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str as IS;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Exceptions\UnknownModelPathException;
use LaravelLang\Filesystem\Filesystem;
use LaravelLang\Models\Eloquent\Translation;
use LaravelLang\Models\Services\ClassMapService;
use LaravelLang\Models\Services\TemplateService;
use UnexpectedValueException;

use function app;
use function collect;
use function implode;
use function is_array;
use function ltrim;

abstract class Generator
{
    protected string $stub;

    /** @var array<string, Model> */
    protected array $registry = [];

    /** @return array<string, string|array<array-key, string>> */
    abstract protected function data(): array;

    abstract protected function filename(): string;

    /** @param  array<array-key, string>  $columns */
    public function __construct(
        protected string $model,
        protected array $columns,
        protected Filesystem $filesystem,
    ) {}

    public function generate(): void
    {
        if ($filename = $this->filename()) {
            $this->store($filename, $this->make());
            $this->finish($filename);
        }
    }

    protected function make(): string
    {
        return TemplateService::replaceFormat(
            template: $this->template(),
            data    : $this->resolveData(),
            format  : '{{%s}}'
        );
    }

    protected function finish(string $path): void {}

    /** @return array{fqn: string, namespace: string, model: string} */
    protected function baseData(): array
    {
        return [
            'fqn'       => $this->getFqn(),
            'namespace' => $this->getNamespace(),
            'model'     => $this->getModel(),
        ];
    }

    /** @return array<string, string> */
    protected function resolveData(): array
    {
        return collect($this->data())
            ->map(fn (mixed $value): string => is_array($value) ? implode(PHP_EOL, $value) : $value)
            ->merge($this->baseData())
            ->all();
    }

    protected function store(string $path, string $content): void
    {
        $this->filesystem->store($path, $content);
    }

    protected function getFqn(): string
    {
        return ltrim($this->model, '\\');
    }

    protected function getNamespace(): string
    {
        return IS::of($this->model)
            ->ltrim('\\')
            ->beforeLast('\\')
            ->toString();
    }

    protected function getModel(): string
    {
        return IS::afterLast($this->model, '\\');
    }

    protected function baseModel(): Model
    {
        return $this->modelInstance($this->model, Model::class);
    }

    protected function translationModel(): Translation
    {
        return $this->modelInstance($this->translationModelNamespace(), Translation::class);
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $expected
     *
     * @return TModel
     */
    protected function modelInstance(string $class, string $expected): Model
    {
        $model = $this->registry[$class] ?? new $class;

        if (! $model instanceof $expected) {
            throw new UnexpectedValueException("Model `$class` must extend `$expected`.");
        }

        return $this->registry[$class] = $model;
    }

    protected function translationModelNamespace(): string
    {
        return $this->model . $this->modelSuffix();
    }

    protected function modelSuffix(): string
    {
        return app(ModelLangConfig::class)->suffix;
    }

    protected function template(): string
    {
        return $this->readTemplate($this->stub);
    }

    protected function readTemplate(string $path): string
    {
        return $this->filesystem->fromString($path);
    }

    protected function path(): string
    {
        if ($path = ClassMapService::path($this->model)) {
            return $path;
        }

        throw new UnknownModelPathException($this->model);
    }
}
