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
        Schema::create('ratings', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('movie_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('liked')->default(false);
            $table->boolean('disliked')->default(false);
            $table->timestamp('rated_at')->nullable()->index('ratings_rated_at_index');
            $table->timestamps();

            $table->primary(['user_id', 'movie_id']);
            $table->index('rating', 'ratings_rating_index');
            $table->index('liked', 'ratings_liked_index');
            $table->index('disliked', 'ratings_disliked_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
