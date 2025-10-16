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
        Schema::create('tv_show_genre', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tv_show_id', 'genre_id']);
            $table->index('genre_id');
        });

        Schema::create('tv_show_language', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tv_show_id', 'language_id']);
            $table->index('language_id');
        });

        Schema::create('tv_show_country', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tv_show_id', 'country_id']);
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_show_country');
        Schema::dropIfExists('tv_show_language');
        Schema::dropIfExists('tv_show_genre');
    }
};
