<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->json('name_translations')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('movie_genre', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['movie_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_genre');
        Schema::dropIfExists('genres');
    }
};
