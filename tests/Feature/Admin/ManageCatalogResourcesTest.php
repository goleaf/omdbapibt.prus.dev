<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ControlPanel;
use App\Livewire\Admin\Crud\ManageCountries;
use App\Livewire\Admin\Crud\ManageGenres;
use App\Livewire\Admin\Crud\ManageLanguages;
use App\Livewire\Admin\Crud\ManageMovies;
use App\Livewire\Admin\Crud\ManagePeople;
use App\Livewire\Admin\Crud\ManageTags;
use App\Livewire\Admin\Crud\ManageTvShows;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Tag;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageCatalogResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panel_route_renders_successfully(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.panel'))
            ->assertOk()
            ->assertSee(trans('ui.admin.panel.title'));
    }

    public function test_admin_can_manage_movies(): void
    {
        $admin = User::factory()->admin()->create();
        $genre = Genre::factory()->create();
        $secondaryGenre = Genre::factory()->create();
        $language = Language::factory()->create(['code' => 'am']);
        $secondaryLanguage = Language::factory()->create(['code' => 'bm']);
        $country = Country::factory()->create(['code' => 'AM']);
        $secondaryCountry = Country::factory()->create(['code' => 'BM']);
        $tag = Tag::factory()->create(['type' => Tag::TYPE_SYSTEM]);
        $secondaryTag = Tag::factory()->create(['type' => Tag::TYPE_SYSTEM]);

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->set('form.title', 'Admin Movie')
            ->set('form.slug', '')
            ->set('form.status', 'Released')
            ->set('form.release_date', '2024-01-01')
            ->set('form.vote_average', '8.5')
            ->set('form.adult', false)
            ->set('form.genre_ids', [$genre->id])
            ->set('form.language_ids', [$language->id])
            ->set('form.country_ids', [$country->id])
            ->set('form.tag_ids', [$tag->id])
            ->call('save')
            ->assertDispatched('record-saved');

        $movie = Movie::query()->where('title->en', 'Admin Movie')->firstOrFail();

        $this->assertDatabaseHas('movie_genre', [
            'movie_id' => $movie->id,
            'genre_id' => $genre->id,
        ]);
        $this->assertDatabaseHas('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $language->id,
        ]);
        $this->assertDatabaseHas('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $country->id,
        ]);
        $this->assertDatabaseHas('film_tag', [
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
            'user_id' => null,
            'weight' => 10,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->call('edit', $movie->id)
            ->set('form.status', 'Archived')
            ->set('form.genre_ids', [$secondaryGenre->id])
            ->set('form.language_ids', [$secondaryLanguage->id])
            ->set('form.country_ids', [$secondaryCountry->id])
            ->set('form.tag_ids', [$secondaryTag->id])
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'status' => 'Archived',
        ]);
        $this->assertDatabaseHas('movie_genre', [
            'movie_id' => $movie->id,
            'genre_id' => $secondaryGenre->id,
        ]);
        $this->assertDatabaseMissing('movie_genre', [
            'movie_id' => $movie->id,
            'genre_id' => $genre->id,
        ]);
        $this->assertDatabaseHas('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $secondaryLanguage->id,
        ]);
        $this->assertDatabaseMissing('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $language->id,
        ]);
        $this->assertDatabaseHas('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $secondaryCountry->id,
        ]);
        $this->assertDatabaseMissing('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $country->id,
        ]);
        $this->assertDatabaseHas('film_tag', [
            'movie_id' => $movie->id,
            'tag_id' => $secondaryTag->id,
        ]);
        $this->assertDatabaseMissing('film_tag', [
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->call('delete', $movie->id)
            ->assertDispatched('record-deleted');

        $this->assertSoftDeleted('movies', ['id' => $movie->id]);
        $this->assertDatabaseMissing('movie_genre', [
            'movie_id' => $movie->id,
            'genre_id' => $secondaryGenre->id,
        ]);
        $this->assertDatabaseMissing('movie_language', [
            'movie_id' => $movie->id,
            'language_id' => $secondaryLanguage->id,
        ]);
        $this->assertDatabaseMissing('movie_country', [
            'movie_id' => $movie->id,
            'country_id' => $secondaryCountry->id,
        ]);
        $this->assertDatabaseMissing('film_tag', [
            'movie_id' => $movie->id,
        ]);
    }

    public function test_admin_can_manage_tags(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageTags::class)
            ->set('form.name.en', 'Spotlight Classic')
            ->set('form.slug', '')
            ->set('form.type', Tag::TYPE_SYSTEM)
            ->call('save')
            ->assertDispatched('record-saved');

        $tag = Tag::query()->where('name_i18n->en', 'Spotlight Classic')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageTags::class)
            ->call('edit', $tag->id)
            ->set('form.name.en', 'Spotlight Prime')
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertSame('Spotlight Prime', $tag->fresh()->name_i18n['en']);

        $targetTag = Tag::factory()->create(['type' => Tag::TYPE_SYSTEM]);

        $movie = Movie::factory()->create();
        $movie->tags()->attach($tag->id, ['user_id' => null, 'weight' => 30]);

        Livewire::actingAs($admin)
            ->test(ManageTags::class)
            ->set('merge.source_id', (string) $tag->id)
            ->set('merge.target_id', (string) $targetTag->id)
            ->call('merge')
            ->assertDispatched('tags-merged');

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseHas('film_tag', [
            'movie_id' => $movie->id,
            'tag_id' => $targetTag->id,
        ]);
    }

    public function test_admin_can_manage_tv_shows(): void
    {
        $admin = User::factory()->admin()->create();
        $genre = Genre::factory()->create();
        $secondaryGenre = Genre::factory()->create();
        $language = Language::factory()->create(['code' => 'cm']);
        $secondaryLanguage = Language::factory()->create(['code' => 'dm']);
        $country = Country::factory()->create(['code' => 'CM']);
        $secondaryCountry = Country::factory()->create(['code' => 'DM']);

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->set('form.name', 'Admin Series')
            ->set('form.slug', '')
            ->set('form.status', 'Running')
            ->set('form.first_air_date', '2023-09-05')
            ->set('form.vote_average', '7.2')
            ->set('form.adult', false)
            ->set('form.genre_ids', [$genre->id])
            ->set('form.language_ids', [$language->id])
            ->set('form.country_ids', [$country->id])
            ->call('save')
            ->assertDispatched('record-saved');

        $show = TvShow::query()->where('name', 'Admin Series')->firstOrFail();

        $this->assertDatabaseHas('tv_show_genre', [
            'tv_show_id' => $show->id,
            'genre_id' => $genre->id,
        ]);
        $this->assertDatabaseHas('tv_show_language', [
            'tv_show_id' => $show->id,
            'language_id' => $language->id,
        ]);
        $this->assertDatabaseHas('tv_show_country', [
            'tv_show_id' => $show->id,
            'country_id' => $country->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->call('edit', $show->id)
            ->set('form.status', 'Ended')
            ->set('form.genre_ids', [$secondaryGenre->id])
            ->set('form.language_ids', [$secondaryLanguage->id])
            ->set('form.country_ids', [$secondaryCountry->id])
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('tv_shows', [
            'id' => $show->id,
            'status' => 'Ended',
        ]);
        $this->assertDatabaseHas('tv_show_genre', [
            'tv_show_id' => $show->id,
            'genre_id' => $secondaryGenre->id,
        ]);
        $this->assertDatabaseMissing('tv_show_genre', [
            'tv_show_id' => $show->id,
            'genre_id' => $genre->id,
        ]);
        $this->assertDatabaseHas('tv_show_language', [
            'tv_show_id' => $show->id,
            'language_id' => $secondaryLanguage->id,
        ]);
        $this->assertDatabaseMissing('tv_show_language', [
            'tv_show_id' => $show->id,
            'language_id' => $language->id,
        ]);
        $this->assertDatabaseHas('tv_show_country', [
            'tv_show_id' => $show->id,
            'country_id' => $secondaryCountry->id,
        ]);
        $this->assertDatabaseMissing('tv_show_country', [
            'tv_show_id' => $show->id,
            'country_id' => $country->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->call('delete', $show->id)
            ->assertDispatched('record-deleted');

        $this->assertSoftDeleted('tv_shows', ['id' => $show->id]);
        $this->assertDatabaseMissing('tv_show_genre', [
            'tv_show_id' => $show->id,
            'genre_id' => $secondaryGenre->id,
        ]);
        $this->assertDatabaseMissing('tv_show_language', [
            'tv_show_id' => $show->id,
            'language_id' => $secondaryLanguage->id,
        ]);
        $this->assertDatabaseMissing('tv_show_country', [
            'tv_show_id' => $show->id,
            'country_id' => $secondaryCountry->id,
        ]);
    }

    public function test_admin_can_manage_people(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManagePeople::class)
            ->set('form.name', 'Admin Actor')
            ->set('form.slug', '')
            ->set('form.known_for_department', 'Acting')
            ->set('form.birthday', '1980-02-02')
            ->set('form.gender', '2')
            ->set('form.popularity', '5.25')
            ->call('save')
            ->assertDispatched('record-saved');

        $person = Person::query()->where('name', 'Admin Actor')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManagePeople::class)
            ->call('edit', $person->id)
            ->set('form.popularity', '9.75')
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'popularity' => 9.75,
        ]);

        Livewire::actingAs($admin)
            ->test(ManagePeople::class)
            ->call('delete', $person->id)
            ->assertDispatched('record-deleted');

        $this->assertDatabaseMissing('people', ['id' => $person->id]);
    }

    public function test_admin_can_manage_genres(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageGenres::class)
            ->set('form.name', 'Admin Genre')
            ->set('form.slug', '')
            ->set('form.tmdb_id', '451')
            ->call('save')
            ->assertDispatched('record-saved');

        $genre = Genre::query()->where('slug', 'admin-genre')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageGenres::class)
            ->call('edit', $genre->id)
            ->set('form.tmdb_id', '452')
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'tmdb_id' => 452,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageGenres::class)
            ->call('delete', $genre->id)
            ->assertDispatched('record-deleted');

        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }

    public function test_admin_can_manage_languages(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageLanguages::class)
            ->set('form.name', 'Admin Language')
            ->set('form.code', 'al')
            ->set('form.native_name', 'Langue Admin')
            ->set('form.active', true)
            ->call('save')
            ->assertDispatched('record-saved');

        $language = Language::query()->where('code', 'al')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageLanguages::class)
            ->call('edit', $language->id)
            ->set('form.active', false)
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'active' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageLanguages::class)
            ->call('delete', $language->id)
            ->assertDispatched('record-deleted');

        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    public function test_admin_can_manage_countries(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageCountries::class)
            ->set('form.name', 'Admin Country')
            ->set('form.code', 'AC')
            ->set('form.active', true)
            ->call('save')
            ->assertDispatched('record-saved');

        $country = Country::query()->where('code', 'AC')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageCountries::class)
            ->call('edit', $country->id)
            ->set('form.active', false)
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'active' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageCountries::class)
            ->call('delete', $country->id)
            ->assertDispatched('record-deleted');

        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }

    public function test_non_admin_users_cannot_access_control_panel(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ControlPanel::class)
            ->assertForbidden();
    }
}
