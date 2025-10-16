<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omdb_api_key_progress', function (Blueprint $table): void {
            $table->id();
            $table->string('sequence_cursor', 8)->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omdb_api_key_progress');
    }
};
