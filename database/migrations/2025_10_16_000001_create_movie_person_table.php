<?php

declare(strict_types=1);

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
        if (Schema::hasTable('movie_person')) {
            return;
        }

        Schema::create('movie_person', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->string('role', 100)->default('cast');
            $table->string('character')->nullable();
            $table->string('job')->nullable();
            $table->string('department')->nullable();
            $table->unsignedSmallInteger('credit_order')->nullable();
            $table->timestamps();

            $table->index('role');
            $table->index('department');
            $table->index('credit_order');
            $table->unique(['movie_id', 'person_id', 'role', 'credit_order'], 'movie_person_unique_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_person');
    }
};
