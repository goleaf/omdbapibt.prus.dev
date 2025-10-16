<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ControlPanel;
use App\Livewire\Admin\Crud\ManageCountries;
use App\Livewire\Admin\Crud\ManageGenres;
use App\Livewire\Admin\Crud\ManageLanguages;
use App\Livewire\Admin\Crud\ManageMovies;
use App\Livewire\Admin\Crud\ManagePeople;
use App\Livewire\Admin\Crud\ManageTvShows;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
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

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->set('form.title', 'Admin Movie')
            ->set('form.slug', '')
            ->set('form.status', 'Released')
            ->set('form.release_date', '2024-01-01')
            ->set('form.vote_average', '8.5')
            ->set('form.adult', false)
            ->call('save')
            ->assertDispatched('record-saved');

        $movie = Movie::query()->where('title->en', 'Admin Movie')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->call('edit', $movie->id)
            ->set('form.status', 'Archived')
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'status' => 'Archived',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageMovies::class)
            ->call('delete', $movie->id)
            ->assertDispatched('record-deleted');

        $this->assertSoftDeleted('movies', ['id' => $movie->id]);
    }

    public function test_admin_can_manage_tv_shows(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->set('form.name', 'Admin Series')
            ->set('form.slug', '')
            ->set('form.status', 'Running')
            ->set('form.first_air_date', '2023-09-05')
            ->set('form.vote_average', '7.2')
            ->set('form.adult', false)
            ->call('save')
            ->assertDispatched('record-saved');

        $show = TvShow::query()->where('name', 'Admin Series')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->call('edit', $show->id)
            ->set('form.status', 'Ended')
            ->call('save')
            ->assertDispatched('record-saved');

        $this->assertDatabaseHas('tv_shows', [
            'id' => $show->id,
            'status' => 'Ended',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageTvShows::class)
            ->call('delete', $show->id)
            ->assertDispatched('record-deleted');

        $this->assertSoftDeleted('tv_shows', ['id' => $show->id]);
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
