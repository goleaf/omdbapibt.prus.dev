<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omdb_api_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 8)->unique();
            $table->timestampTz('first_seen_at')->nullable();
            $table->timestampTz('last_checked_at')->nullable();
            $table->timestampTz('last_confirmed_at')->nullable();
            $table->unsignedSmallInteger('last_response_code')->nullable();
            $table->enum('status', ['pending', 'valid', 'invalid', 'unknown'])->nullable()->index();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omdb_api_keys');
    }
};
