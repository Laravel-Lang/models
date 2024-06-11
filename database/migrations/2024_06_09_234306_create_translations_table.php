<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelLang\Config\Facades\Config;

return new class extends Migration {
    public function up(): void
    {
        $config = $this->config();

        Schema::connection($config->connection)
            ->create($config->table, function (Blueprint $table) use ($config) {
                $table->id();

                $table->string('model_type', 255);
                $table->string('model_id', 255);

                $table->jsonb('content');

                $table->timestamps();
                $table->softDeletes();
            });

        DB::statement(
            "CREATE UNIQUE INDEX {$config->table}_model_type_model_id_unique ON {$config->table} (model_type, model_id) WHERE deleted_at IS NULL"
        );
    }

    public function down(): void
    {
        $config = $this->config();

        Schema::connection($config->connection)->dropIfExists($config->table);
    }

    protected function config(): ModelsData
    {
        return Config::shared()->models;
    }
};
