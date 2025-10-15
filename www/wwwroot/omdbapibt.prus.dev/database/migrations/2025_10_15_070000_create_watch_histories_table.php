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
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('watchable');
            $table->string('content_title');
            $table->string('content_type', 50)->index();
            $table->enum('status', ['in_progress', 'completed'])->default('completed')->index();
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->dateTime('viewed_at')->useCurrent()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
