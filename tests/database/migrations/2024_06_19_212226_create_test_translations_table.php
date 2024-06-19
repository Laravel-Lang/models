<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Fixtures\Models\TestModel;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_model_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(TestModel::class, 'item_id')->constrained()->cascadeOnDelete();

            $table->string('locale');

            $table->string('title')->nullable();
            $table->string('description')->nullable();

            $table->unique(['item_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_model_translations');
    }
};
