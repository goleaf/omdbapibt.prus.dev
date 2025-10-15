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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('imdb_id', 20)->nullable()->unique();
            $table->string('slug')->nullable()->unique();
            $table->string('name');
            $table->json('also_known_as')->nullable();
            $table->json('biography')->nullable();
            $table->date('birthday')->nullable();
            $table->date('deathday')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->unsignedTinyInteger('gender')->nullable();
            $table->string('known_for_department')->nullable();
            $table->decimal('popularity', 10, 3)->nullable();
            $table->string('homepage')->nullable();
            $table->string('profile_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('slug');
            $table->index('known_for_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
