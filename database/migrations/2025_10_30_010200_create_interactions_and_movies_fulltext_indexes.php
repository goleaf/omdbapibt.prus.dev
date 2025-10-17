<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable; // kept for non-sqlite drivers; handled in handleDriverException()

return new class extends Migration
{
    public function up(): void
    {
        $this->createInteractionsTable();
        $this->addMovieFullTextIndex();
    }

    public function down(): void
    {
        $this->dropMovieFullTextIndex();
        $this->dropInteractionsTable();
    }

    private function createInteractionsTable(): void
    {
        if (Schema::hasTable('interactions')) {
            return;
        }

        try {
            Schema::create('interactions', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('movie_id')->nullable()->constrained('movies')->cascadeOnDelete();
                $table->string('type');
                $table->json('payload')->nullable();
                $table->timestamp('occurred_at')->useCurrent();
                $table->timestamps();

                $table->index(['user_id', 'type']);
                $table->index(['movie_id', 'type']);
            });
        } catch (Throwable $exception) {
            $this->handleDriverException($exception);
        }
    }

    private function addMovieFullTextIndex(): void
    {
        if (! Schema::hasTable('movies')) {
            return;
        }

        if (! $this->supportsFullText()) {
            return;
        }

        try {
            Schema::table('movies', function (Blueprint $table): void {
                $table->fullText(['title', 'overview'], 'movies_title_overview_fulltext');
            });
        } catch (Throwable $exception) {
            $this->handleDriverException($exception);
        }
    }

    private function dropMovieFullTextIndex(): void
    {
        if (! Schema::hasTable('movies')) {
            return;
        }

        if (! $this->supportsFullText()) {
            return;
        }

        try {
            Schema::table('movies', function (Blueprint $table): void {
                $table->dropFullText('movies_title_overview_fulltext');
            });
        } catch (Throwable $exception) {
            $this->handleDriverException($exception);
        }
    }

    private function dropInteractionsTable(): void
    {
        if (! Schema::hasTable('interactions')) {
            return;
        }

        try {
            Schema::dropIfExists('interactions');
        } catch (Throwable $exception) {
            $this->handleDriverException($exception);
        }
    }

    private function handleDriverException(Throwable $exception): void
    {
        if ($this->isSqlite()) {
            return;
        }

        throw $exception;
    }

    private function supportsFullText(): bool
    {
        return in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
