<?php

namespace Tests\Feature\Database;

use App\Models\Interaction;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\Platform;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NewSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_expected_tables_exist(): void
    {
        $tables = [
            'tags',
            'film_tag',
            'movie_tag',
            'ratings',
            'lists',
            'list_items',
            'follows',
            'recommendations',
            'platforms',
            'movie_platform',
            'interactions',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                sprintf('Failed asserting that the %s table exists.', $table),
            );
        }
    }

    public function test_film_tag_unique_constraint_prevents_duplicate_rows(): void
    {
        $movie = Movie::factory()->create();
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        DB::table('film_tag')->insert([
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
            'user_id' => $user->id,
            'weight' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('film_tag')->insert([
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
            'user_id' => $user->id,
            'weight' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_movie_platform_pivot_enforces_primary_key_uniqueness(): void
    {
        $movie = Movie::factory()->create();
        $platform = Platform::factory()->create();

        DB::table('movie_platform')->insert([
            'movie_id' => $movie->id,
            'platform_id' => $platform->id,
            'availability' => 'available',
            'link' => 'https://example.com/watch',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('movie_platform')->insert([
            'movie_id' => $movie->id,
            'platform_id' => $platform->id,
            'availability' => 'available',
            'link' => 'https://example.com/watch',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_movie_can_sync_tags_with_weights_and_users(): void
    {
        $movie = Movie::factory()->create();
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $movie->tags()->sync([
            $tags[0]->id => ['user_id' => $user->id, 'weight' => 5],
            $tags[1]->id => ['user_id' => $user->id, 'weight' => 2],
        ]);

        $movie->refresh();

        $this->assertCount(2, $movie->tags);
        $this->assertSame(5, $movie->tags->firstWhere('id', $tags[0]->id)?->pivot->weight);
        $this->assertSame($user->id, $movie->tags->firstWhere('id', $tags[1]->id)?->pivot->user_id);
    }

    public function test_platforms_attach_to_movies_through_pivot(): void
    {
        $platform = Platform::factory()->create();
        $movie = Movie::factory()->create();

        $platform->movies()->attach($movie->id, [
            'availability' => 'streaming',
            'link' => 'https://example.com/stream',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($platform->movies()->whereKey($movie->id)->exists());
    }

    public function test_lists_attach_movies_and_expose_positions(): void
    {
        $list = ListModel::factory()->create();
        $movie = Movie::factory()->create();

        $list->movies()->attach($movie->id, [
            'position' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $list->refresh();

        $this->assertTrue($list->movies()->whereKey($movie->id)->exists());
        $this->assertSame(1, $list->items()->first()->position);
    }

    public function test_interactions_resolve_related_models(): void
    {
        $interaction = Interaction::factory()->create();

        $this->assertNotNull($interaction->user);
        $this->assertNotNull($interaction->movie);
    }
}
