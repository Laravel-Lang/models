<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

use LaravelLang\Exceptions\UnknownModelPathException;
use LaravelLang\Models\HasTranslations;
use LaravelLang\Models\Services\ClassUsesService;
use PhpToken;
use ReflectionClass;

use function array_filter;
use function array_values;
use function class_exists;
use function is_string;
use function realpath;
use function str_contains;
use function str_starts_with;
use function strcasecmp;
use function substr;
use function substr_replace;

class ParentGenerator extends Generator
{
    protected ?string $template = null;

    protected function make(): string
    {
        return $this->wasAttached()
            ? $this->template()
            : $this->attach();
    }

    protected function filename(): string
    {
        return $this->path();
    }

    protected function template(): string
    {
        return $this->template ??= $this->readTemplate($this->path());
    }

    /** @return array{} */
    protected function data(): array
    {
        return [];
    }

    protected function attach(): string
    {
        $class = $this->getFqn();
        $model = class_exists($class) ? new ReflectionClass($class) : null;
        $file  = $model?->getFileName();

        if ($model === null || ! is_string($file) || realpath($file) !== realpath($this->path())) {
            throw new UnknownModelPathException($this->model);
        }

        $source = $this->template();
        $tokens = array_values(array_filter(
            PhpToken::tokenize($source, TOKEN_PARSE),
            static fn (PhpToken $token): bool => ! $token->isIgnorable(),
        ));
        $namespace = '';

        foreach ($tokens as $index => $token) {
            if ($token->is(T_NAMESPACE)) {
                $name      = $tokens[$index + 1];
                $namespace = $name->is([T_STRING, T_NAME_QUALIFIED]) ? $name->text . '\\' : '';
            }

            if (! $token->is(T_CLASS) || $token->line !== $model->getStartLine()) {
                continue;
            }

            $name = $tokens[$index + 1];

            if (! $name->is(T_STRING) || strcasecmp($namespace . $name->text, $model->getName()) !== 0) {
                continue;
            }

            $body = $index + 2;

            while (! $tokens[$body]->is('{')) {
                $body++;
            }

            $offset    = $tokens[$body]->pos + 1;
            $newLine   = str_contains($source, "\r\n") ? "\r\n" : "\n";
            $insertion = $newLine . '    use \\' . HasTranslations::class . ';';

            if (! str_starts_with(substr($source, $offset), $newLine)) {
                $insertion .= $newLine;
            }

            $updated = substr_replace($source, $insertion, $offset, 0);
            PhpToken::tokenize($updated, TOKEN_PARSE);

            return $updated;
        }

        throw new UnknownModelPathException($this->model);
    }

    protected function wasAttached(): bool
    {
        return ClassUsesService::usesTrait($this->getFqn(), HasTranslations::class);
    }
}
