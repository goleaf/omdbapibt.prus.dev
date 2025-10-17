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
        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name_i18n');
            $table->string('type')->default('system');
            $table->timestamps();

            $table->index('type');
        });

        Schema::create('film_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('weight')->default(0);
            $table->timestamps();

            $table->unique(['movie_id', 'tag_id', 'user_id']);
            $table->index('tag_id');
            $table->index('user_id');
            $table->index('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_tag');
        Schema::dropIfExists('tags');
    }
};
