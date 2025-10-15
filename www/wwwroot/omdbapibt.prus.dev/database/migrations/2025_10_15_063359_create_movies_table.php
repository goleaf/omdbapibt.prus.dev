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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('imdb_id', 20)->nullable()->unique();
            $table->string('omdb_id', 50)->nullable()->index();
            $table->string('slug')->nullable()->unique();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('runtime')->nullable();
            $table->date('release_date')->nullable();
            $table->text('plot')->nullable();
            $table->string('tagline')->nullable();
            $table->string('homepage')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->unsignedBigInteger('revenue')->nullable();
            $table->string('status')->nullable();
            $table->decimal('popularity', 10, 3)->nullable();
            $table->decimal('vote_average', 3, 1)->nullable();
            $table->unsignedInteger('vote_count')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('media_type', 50)->nullable();
            $table->boolean('adult')->default(false);
            $table->boolean('video')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['title']);
            $table->index(['popularity']);
            $table->index(['vote_average']);
        });

        if ($this->usingMySql()) {
            Schema::table('movies', function (Blueprint $table) {
                $table->fullText('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }

    private function usingMySql(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }
};
