<?php

declare(strict_types=1);

namespace LaravelLang\Models\Services;

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Database\Eloquent\Model;
use LaravelLang\Config\Facades\Config;

class HelperGenerator
{
    protected string $template = '     * @property array|string|ContentData $%s';

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

    protected function getProperties(): array
    {
        return array_map(
            fn (string $attribute) => sprintf($this->template, $attribute),
            $this->getTranslatable($this->class)
        );
    }

    protected function getNamespace(): string
    {
        return dirname($this->class);
    }

    protected function getName(): string
    {
        return class_basename($this->class);
    }

    protected function getHash(): string
    {
        return md5($this->class);
    }

    protected function getTranslatable(string $class): array
    {
        return $this->initializeModel($class)->translatable();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\LaravelLang\Models\HasTranslations
     */
    protected function initializeModel(string $class): Model
    {
        return new $class();
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
