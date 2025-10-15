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
        if (Schema::hasTable('person_tv_show')) {
            return;
        }

        Schema::create('person_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->string('role', 100)->default('cast');
            $table->string('character')->nullable();
            $table->string('job')->nullable();
            $table->string('department')->nullable();
            $table->unsignedSmallInteger('credit_order')->nullable();
            $table->timestamps();

            $table->index('role');
            $table->index('department');
            $table->index('credit_order');
            $table->unique(['person_id', 'tv_show_id', 'role', 'credit_order'], 'person_tv_show_unique_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_tv_show');
    }
};
