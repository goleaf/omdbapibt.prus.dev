<?php

namespace App\Livewire;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MovieList extends Component
{
    use WithPagination;

    #[Url(as: 'genre', except: null, history: true)]
    public ?int $genreId = null;

    #[Url(as: 'year', except: null, history: true)]
    public ?int $year = null;

    #[Url(as: 'rating', except: null, history: true)]
    public ?float $rating = null;

    #[Url(as: 'language', except: null, history: true)]
    public ?int $languageId = null;

    #[Url(as: 'country', except: null, history: true)]
    public ?int $countryId = null;

    protected $listeners = [
        'refreshMovies' => '$refresh',
    ];

    /**
     * Reset the pagination when a filter is updated.
     */
    public function updated($property): void
    {
        if (in_array($property, ['genreId', 'year', 'rating', 'languageId', 'countryId'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Clear the provided filter.
     */
    public function clear(string $filter): void
    {
        if (! property_exists($this, $filter)) {
            return;
        }

        $this->{$filter} = null;
        $this->resetPage();
    }

    /**
     * Clear all filters at once.
     */
    public function clearAll(): void
    {
        $this->genreId = null;
        $this->year = null;
        $this->rating = null;
        $this->languageId = null;
        $this->countryId = null;

        $this->resetPage();
    }

    /**
     * Build the base movie query with active filters.
     */
    protected function movieQuery(): Builder
    {
        return Movie::query()
            ->with([
                'genres:id,slug,name_translations',
                'languages:id,code,name_translations,native_name_translations',
                'countries:id,code,name_translations',
            ])
            ->when($this->genreId, function (Builder $query): Builder {
                return $query->whereHas('genres', fn (Builder $genreQuery) => $genreQuery->whereKey($this->genreId));
            })
            ->when($this->languageId, function (Builder $query): Builder {
                return $query->whereHas('languages', fn (Builder $languageQuery) => $languageQuery->whereKey($this->languageId));
            })
            ->when($this->countryId, function (Builder $query): Builder {
                return $query->whereHas('countries', fn (Builder $countryQuery) => $countryQuery->whereKey($this->countryId));
            })
            ->when($this->year, function (Builder $query): Builder {
                return $query->where(function (Builder $innerQuery) {
                    $innerQuery->where('year', $this->year)
                        ->orWhereYear('release_date', $this->year);
                });
            })
            ->when(! is_null($this->rating), function (Builder $query): Builder {
                return $query->whereVoteAverageAtLeast($this->rating ?? 0);
            })
            ->orderByDesc('release_date')
            ->orderByDesc('year')
            ->orderBy('title');
    }

    /**
     * Retrieve the paginated movies collection.
     */
    protected function movies(): LengthAwarePaginator
    {
        return $this->movieQuery()->paginate(perPage: 12, pageName: 'page');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $movies = $this->movies();

        $locale = app()->getLocale() ?? config('app.fallback_locale', 'en');

        $genres = Genre::query()
            ->get(['id', 'slug', 'name_translations'])
            ->sortBy(fn (Genre $genre) => Str::lower($genre->localizedName($locale) ?? ''))
            ->values();

        $languages = Language::query()
            ->get(['id', 'code', 'name_translations', 'native_name_translations'])
            ->sortBy(fn (Language $language) => Str::lower($language->localizedName($locale) ?? ''))
            ->values();

        $countries = Country::query()
            ->get(['id', 'code', 'name_translations'])
            ->sortBy(fn (Country $country) => Str::lower($country->localizedName($locale) ?? ''))
            ->values();

        return view('livewire.movie-list', [
            'movies' => $movies,
            'genres' => $genres,
            'languages' => $languages,
            'countries' => $countries,
            'availableYears' => Movie::query()
                ->selectRaw('DISTINCT COALESCE(year, YEAR(release_date)) as filter_year')
                ->where(function ($query): void {
                    $query->whereNotNull('year')->orWhereNotNull('release_date');
                })
                ->orderByDesc('filter_year')
                ->pluck('filter_year')
                ->filter()
                ->values(),
            'ratingOptions' => collect([9.0, 8.0, 7.0, 6.0, 5.0]),
        ]);
    }
}
