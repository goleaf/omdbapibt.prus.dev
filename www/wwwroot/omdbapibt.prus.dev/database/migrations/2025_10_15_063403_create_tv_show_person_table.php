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
        Schema::create('tv_show_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50)->default('cast')->index();
            $table->string('department')->nullable();
            $table->string('character')->nullable();
            $table->string('job')->nullable();
            $table->unsignedSmallInteger('order')->nullable();
            $table->timestamps();

            $table->unique(['tv_show_id', 'person_id', 'role', 'character', 'job'], 'tv_show_person_unique_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_show_person');
    }
};
