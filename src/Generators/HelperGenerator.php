<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

use Illuminate\Support\Str;
use LaravelLang\Config\Services\ModelLangConfig;

use function array_map;
use function hash;
use function sprintf;

class HelperGenerator extends Generator
{
    protected string $stub = __DIR__ . '/../../stubs/helper.stub';

    protected string $properties = '     * @property string|null $%s';

    /** @return array{hash: string, properties: array<array-key, string>, translationNamespace: string, translationModel: string} */
    protected function data(): array
    {
        return [
            'hash'       => $this->getHash(),
            'properties' => $this->getProperties(),

            'translationNamespace' => $this->translationModelNamespace(),
            'translationModel'     => $this->getTranslationModel(),
        ];
    }

    protected function filename(): string
    {
        return app(ModelLangConfig::class)->helpers . '/_ide_helper_models_' . $this->getHash() . '.php';
    }

    /** @return array<array-key, string> */
    protected function getProperties(): array
    {
        return array_map(
            fn (string $attribute): string => sprintf($this->properties, $attribute),
            $this->getTranslatable()
        );
    }

    protected function getHash(): string
    {
        return hash('xxh128', $this->model);
    }

    /** @return array<array-key, string> */
    protected function getTranslatable(): array
    {
        return $this->translationModel()->translatable();
    }

    protected function getTranslationModel(): string
    {
        return Str::afterLast($this->translationModelNamespace(), '\\');
    }
}
