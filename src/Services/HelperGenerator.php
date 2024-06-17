<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Str as IS;
use LaravelLang\Config\Facades\Config;
use LaravelLang\Models\Eloquent\Translation;

class HelperGenerator
{
    protected string $template = '     * @property array|string|null $%s';

    protected string $filenamePrefix = '_ide_helper_models_';

    public function __construct(
        protected string $class
    ) {}

    public static function of(string $class): static
    {
        return new static($class);
    }

    public function generate(): void
    {
        $this->store(
            $this->make()
        );
    }

    protected function make(): string
    {
        return Str::of($this->stub())->replaceFormat([
            'namespace'  => $this->getNamespace(),
            'model'      => $this->getName(),
            'hash'       => $this->getHash(),
            'properties' => $this->getProperties(),
        ], '{{%s}}')->toString();
    }

    protected function getProperties(): string
    {
        return $this->getTranslatable($this->class)
            ->map(fn (string $attribute) => sprintf($this->template, $attribute))
            ->implode(PHP_EOL);
    }

    protected function getNamespace(): string
    {
        return IS::beforeLast($this->class, '\\');
    }

    protected function getName(): string
    {
        return class_basename($this->class);
    }

    protected function getHash(): string
    {
        return md5($this->class);
    }

    protected function getTranslatable(string $class): Collection
    {
        return collect($this->initializeModel($class)->getFillable())
            ->reject(fn (string $value) => strtolower($value) === 'locale');
    }

    protected function initializeModel(string $class): Translation
    {
        return new ($class . $this->modelSuffix())();
    }

    protected function modelSuffix(): string
    {
        return IS::ucfirst(Config::shared()->models->suffix);
    }

    protected function stub(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/helper.stub');
    }

    protected function store(string $content): void
    {
        File::store($this->filename(), $content);
    }

    protected function filename(): string
    {
        return Config::shared()->models->helpers . '/' . $this->filenamePrefix . $this->getHash() . '.php';
    }
}
