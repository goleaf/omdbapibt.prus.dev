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
        Schema::create('movie_person', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->string('credit_type')->default('cast');
            $table->string('department')->nullable();
            $table->string('character')->nullable();
            $table->string('job')->nullable();
            $table->unsignedInteger('credit_order')->nullable();
            $table->timestamps();

            $table->index(['movie_id', 'credit_type']);
            $table->index(['person_id', 'credit_type']);
        });

        Schema::create('tv_show_person', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->string('credit_type')->default('cast');
            $table->string('department')->nullable();
            $table->string('character')->nullable();
            $table->string('job')->nullable();
            $table->unsignedInteger('credit_order')->nullable();
            $table->timestamps();

            $table->index(['tv_show_id', 'credit_type']);
            $table->index(['person_id', 'credit_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_show_person');
        Schema::dropIfExists('movie_person');
    }
};
