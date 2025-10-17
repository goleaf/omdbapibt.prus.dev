<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tags and movie_tag tables are created in 2025_10_20_050000_create_tags_tables.php

        if (! Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table): void {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('review')->nullable();
                $table->timestamp('rated_at')->useCurrent();
                $table->timestamps();

                $table->primary(['user_id', 'movie_id']);
                $table->index(['movie_id', 'rating']);
            });
        }

        if (! Schema::hasTable('lists')) {
            Schema::create('lists', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(false);
                $table->timestamps();

                $table->index('user_id');
            });
        }

        if (! Schema::hasTable('list_items')) {
            Schema::create('list_items', function (Blueprint $table): void {
                $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();
                $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
                $table->unsignedInteger('position')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->primary(['list_id', 'movie_id']);
                $table->index(['list_id', 'position']);
            });
        }

        if (! Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table): void {
                $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('followed_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['follower_id', 'followed_id']);
                $table->index('followed_id');
            });
        }

        if (! Schema::hasTable('recommendations')) {
            Schema::create('recommendations', function (Blueprint $table): void {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('algo');
                $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
                $table->unsignedSmallInteger('rank')->nullable();
                $table->decimal('score', 6, 3)->nullable();
                $table->json('context')->nullable();
                $table->timestamps();

                $table->primary(['user_id', 'algo', 'movie_id']);
                $table->index(['user_id', 'score']);
            });
        }

        if (! Schema::hasTable('platforms')) {
            Schema::create('platforms', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('movie_platform')) {
            Schema::create('movie_platform', function (Blueprint $table): void {
                $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
                $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
                $table->string('availability')->nullable();
                $table->string('link')->nullable();
                $table->timestamps();

                $table->primary(['movie_id', 'platform_id']);
                $table->index('platform_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('movie_platform')) {
            Schema::dropIfExists('movie_platform');
        }

        if (Schema::hasTable('platforms')) {
            Schema::dropIfExists('platforms');
        }

        if (Schema::hasTable('recommendations')) {
            Schema::dropIfExists('recommendations');
        }

        if (Schema::hasTable('follows')) {
            Schema::dropIfExists('follows');
        }

        if (Schema::hasTable('list_items')) {
            Schema::dropIfExists('list_items');
        }

        if (Schema::hasTable('lists')) {
            Schema::dropIfExists('lists');
        }

        if (Schema::hasTable('ratings')) {
            Schema::dropIfExists('ratings');
        }

        // Tags and movie_tag tables are dropped in their own migration
    }
};
