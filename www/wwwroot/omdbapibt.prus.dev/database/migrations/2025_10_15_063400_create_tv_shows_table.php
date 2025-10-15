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
        Schema::create('tv_shows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('imdb_id', 20)->nullable()->unique();
            $table->string('slug')->nullable()->unique();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->date('first_air_date')->nullable();
            $table->date('last_air_date')->nullable();
            $table->unsignedSmallInteger('number_of_seasons')->nullable();
            $table->unsignedSmallInteger('number_of_episodes')->nullable();
            $table->unsignedSmallInteger('episode_run_time')->nullable();
            $table->string('status')->nullable();
            $table->text('overview')->nullable();
            $table->string('tagline')->nullable();
            $table->string('homepage')->nullable();
            $table->decimal('popularity', 10, 3)->nullable();
            $table->decimal('vote_average', 3, 1)->nullable();
            $table->unsignedInteger('vote_count')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('media_type', 50)->nullable();
            $table->boolean('adult')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name']);
            $table->index(['popularity']);
            $table->index(['vote_average']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
