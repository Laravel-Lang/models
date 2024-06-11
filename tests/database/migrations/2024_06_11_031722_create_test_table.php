<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();

            $table->string('key');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
