<?php

namespace Tests\Unit;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_cast_returns_localized_array(): void
    {
        $titles = [
            'en' => 'Interstellar',
            'es' => 'Interestelar',
        ];

        $movie = Movie::factory()->create([
            'title' => $titles,
        ]);

        $this->assertSame($titles, $movie->fresh()->title);
        $this->assertSame('Interstellar', $movie->fresh()->localizedTitle('en'));
        $this->assertSame('Interestelar', $movie->fresh()->localizedTitle('es'));
    }
}
