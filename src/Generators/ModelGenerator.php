<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

use DragonCode\Support\Facades\Filesystem\Path;
use LaravelLang\Models\Services\ClassMap;

class ModelGenerator extends Generator
{
    protected string $stub = __DIR__ . '/../../stubs/model.stub';

    protected string $fillables = '        \'%s\',';

    protected function data(): array
    {
        return [
            'suffix'   => $this->modelSuffix(),
            'fillable' => $this->getFillable(),
        ];
    }

    protected function filename(): string
    {
        $directory = dirname($path = $this->path());
        $filename  = $this->getModel() . $this->modelSuffix();
        $extension = $this->extension($path);

        return $directory . '/' . $filename . '.' . $extension;
    }

    protected function getFillable(): array
    {
        return array_map(function (string $attribute) {
            return sprintf($this->fillables, $attribute);
        }, $this->columns);
    }

    protected function path(): string
    {
        return ClassMap::path($this->model);
    }

    protected function extension(string $path): string
    {
        return Path::extension($path);
    }
}
