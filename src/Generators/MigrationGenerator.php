<?php

declare(strict_types=1);

namespace LaravelLang\Models\Generators;

class MigrationGenerator extends Generator
{
    protected string $stub = __DIR__ . '/../../stubs/migration.stub';

    protected string $column = '            $table->string(\'%s\')->nullable();';

    protected function data(): array
    {
        return [
            'table'   => $this->getTable(),
            'columns' => $this->getColumns(),
        ];
    }

    protected function filename(): string
    {
        return database_path(sprintf('migrations/%s_create_%s_table.php', date('Y_m_d_His'), $this->getTable()));
    }

    protected function getTable(): string
    {
        return $this->translationModel()->getTable();
    }

    protected function getColumns(): array
    {
        return array_map(fn (string $attribute) => sprintf($this->column, $attribute), $this->columns);
    }
}
