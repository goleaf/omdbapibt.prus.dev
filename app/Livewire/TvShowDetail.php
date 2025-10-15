<?php

namespace App\Livewire;

use App\Models\TvShow;
use App\Support\TvShowRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class TvShowDetail extends Component
{
    public string $locale;

    /**
     * @var array<string, mixed>
     */
    public array $show = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $seasons = [];

    /**
     * @var array{cast: array<int, array<string, mixed>>, crew: array<int, array<string, mixed>>}
     */
    public array $credits = [
        'cast' => [],
        'crew' => [],
    ];

    public ?int $tvShowId = null;

    public function mount(TvShowRepository $repository, string $show): void
    {
        $this->locale = request()->route('locale') ?? App::getLocale();
        App::setLocale($this->locale);

        try {
            $this->show = $repository->findBySlugOrId($show);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $this->seasons = $repository->seasonsFor($this->show);
        $this->credits = $repository->creditsFor($this->show);

        $slug = $this->show['slug'] ?? null;

        if ($slug) {
            $this->tvShowId = TvShow::where('slug', $slug)->value('id');
        }

        if (! $this->tvShowId && isset($this->show['id'])) {
            $this->tvShowId = TvShow::where('id', $this->show['id'])->value('id');
        }
    }

    public function translate(null|array|string $value): ?string
    {
        if (is_array($value)) {
            if (Arr::has($value, $this->locale)) {
                return $value[$this->locale];
            }

            $fallback = config('app.fallback_locale');

            return $value[$fallback] ?? reset($value) ?: null;
        }

        return $value;
    }

    public function render()
    {
        return view('livewire.tv-show-detail');
    }
}
