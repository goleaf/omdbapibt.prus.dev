<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Carbon;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('user_watchlist')) {
            return;
        }

        $now = Carbon::now();

        $defaultLists = DB::table('lists')
            ->where('title', 'Watch Later')
            ->pluck('id', 'user_id');

        $watchlistEntries = DB::table('user_watchlist')
            ->where('watchlistable_type', 'App\\Models\\Movie')
            ->orderBy('created_at')
            ->get(['user_id', 'watchlistable_id', 'created_at']);

        $grouped = $watchlistEntries->groupBy('user_id');

        foreach ($grouped as $userId => $entries) {
            $listId = $defaultLists[$userId] ?? null;

            if ($listId === null) {
                $listId = DB::table('lists')->insertGetId([
                    'user_id' => $userId,
                    'title' => 'Watch Later',
                    'public' => false,
                    'description' => null,
                    'cover_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $defaultLists[$userId] = $listId;
            }

            $existingPositions = DB::table('list_items')
                ->where('list_id', $listId)
                ->max('position');

            $position = $existingPositions ? (int) $existingPositions : 0;

            foreach ($entries as $entry) {
                $movieId = (int) $entry->watchlistable_id;

                $alreadyExists = DB::table('list_items')
                    ->where('list_id', $listId)
                    ->where('movie_id', $movieId)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $position++;

                DB::table('list_items')->insert([
                    'list_id' => $listId,
                    'movie_id' => $movieId,
                    'position' => $position,
                    'created_at' => $entry->created_at ?? $now,
                    'updated_at' => $entry->created_at ?? $now,
                ]);
            }
        }

        Schema::dropIfExists('user_watchlist');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('user_watchlist')) {
            Schema::create('user_watchlist', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->morphs('watchlistable');
                $table->timestamps();

                $table->unique(['user_id', 'watchlistable_type', 'watchlistable_id'], 'user_watchlist_unique');
            });
        }

        if (! Schema::hasTable('list_items')) {
            return;
        }

        $listItems = DB::table('list_items')
            ->join('lists', 'list_items.list_id', '=', 'lists.id')
            ->select([
                'lists.user_id',
                'list_items.movie_id',
                'list_items.created_at',
            ])
            ->orderBy('list_items.created_at')
            ->get();

        foreach ($listItems as $item) {
            DB::table('user_watchlist')->updateOrInsert(
                [
                    'user_id' => $item->user_id,
                    'watchlistable_type' => 'App\\Models\\Movie',
                    'watchlistable_id' => $item->movie_id,
                ],
                [
                    'created_at' => $item->created_at,
                    'updated_at' => $item->created_at,
                ],
            );
        }
    }
};
