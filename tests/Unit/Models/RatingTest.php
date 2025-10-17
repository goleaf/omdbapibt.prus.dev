<?php

namespace Tests\Unit\Models;

use App\Models\ListItem;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('invalidRatingsProvider')]
    public function test_rating_must_be_between_one_and_ten(int $invalidRating): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $rating = new Rating([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => $invalidRating,
        ]);

        $this->expectException(ValidationException::class);

        $rating->save();
    }

    /**
     * @return array<string, array{0: int}>
     */
    public static function invalidRatingsProvider(): array
    {
        return [
            'zero' => [0],
            'too high' => [11],
        ];
    }

    public function test_rating_persists_within_valid_range(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $rating = Rating::query()->create([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 7,
            'liked' => true,
            'disliked' => false,
        ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 7,
            'liked' => true,
            'disliked' => false,
        ]);

        $this->assertSame(7, $rating->rating);
        $this->assertTrue($rating->liked);
        $this->assertFalse($rating->disliked);
    }

    public function test_liked_and_disliked_flags_are_mutually_exclusive(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $rating = new Rating([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => 5,
            'liked' => true,
            'disliked' => true,
        ]);

        $this->expectException(ValidationException::class);

        $rating->save();
    }

    public function test_movie_tag_pivot_enforces_uniqueness(): void
    {
        $movie = Movie::factory()->create();
        $tag = Tag::factory()->create();

        DB::table('movie_tag')->insert([
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
            'weight' => 1.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('movie_tag')->insert([
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
            'weight' => 1.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_list_items_pivot_enforces_uniqueness(): void
    {
        $list = ListModel::factory()->create();
        $movie = Movie::factory()->create();

        ListItem::factory()->create([
            'list_id' => $list->id,
            'movie_id' => $movie->id,
            'position' => 1,
        ]);

        $this->expectException(QueryException::class);

        ListItem::factory()->create([
            'list_id' => $list->id,
            'movie_id' => $movie->id,
            'position' => 2,
        ]);
    }
}
