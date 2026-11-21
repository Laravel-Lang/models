<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Str;
use LaravelLang\Filesystem\Filesystem;

use function array_map;
use function class_exists;
use function dirname;
use function pathinfo;
use function sprintf;

class ModelGenerator extends Generator
{
    protected string $stub = __DIR__ . '/../../stubs/model.stub';

    protected string $fillable = '\'%s\',';

    protected int $fillablePad = 4;

    protected string $casts = '\'%s\' => TrimStringCast::class,';

    protected int $castsPad = 12;

    /** @param array<array-key, string> $columns */
    public function __construct(string $model, array $columns, Filesystem $filesystem)
    {
        parent::__construct($model, $columns, $filesystem);

        if (! class_exists(Fillable::class)) {
            $this->stub        = __DIR__ . '/../../stubs/model-legacy.stub';
            $this->fillablePad = 8;
        }
    }

    protected function finish(string $path): void
    {
        require $path;
    }

    /** @return array{suffix: string, fillable: array<array-key, string>, casts: array<array-key, string>} */
    protected function data(): array
    {
        return [
            'suffix'   => $this->modelSuffix(),
            'fillable' => $this->getFillable(),
            'casts'    => $this->getCasts(),
        ];
    }

    protected function filename(): string
    {
        $directory = dirname($path = $this->path());
        $filename  = $this->getModel() . $this->modelSuffix();
        $extension = $this->extension($path);

        return $directory . '/' . $filename . '.' . $extension;
    }

    /** @return array<array-key, string> */
    protected function getFillable(): array
    {
        return array_map(
            fn (string $attribute): string => $this->pad(sprintf($this->fillable, $attribute), $this->fillablePad),
            $this->columns
        );
    }

    /** @return array<array-key, string> */
    protected function getCasts(): array
    {
        return array_map(
            fn (string $attribute): string => $this->pad(sprintf($this->casts, $attribute), $this->castsPad),
            $this->columns
        );
    }

    protected function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    protected function pad(string $value, int $length): string
    {
        return Str::of($value)
            ->padLeft(Str::length($value) + $length)
            ->toString();
    }
}
