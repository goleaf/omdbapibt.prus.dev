<?php

namespace Tests\Unit\Database\Factories;

use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TvShowFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_tv_show_factory_sets_expected_defaults(): void
    {
        $show = TvShow::factory()->create();

        $this->assertSame('tv', $show->media_type);
        $this->assertFalse($show->adult);
        $this->assertNotEmpty($show->name);
        $this->assertNotNull($show->first_air_date);
        $this->assertGreaterThan(0, $show->number_of_seasons);
    }
}
