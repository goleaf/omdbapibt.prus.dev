<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvShow extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'dedup_hash',
        'slug',
        'name',
        'name_translations',
        'original_name',
        'first_air_date',
        'last_air_date',
        'number_of_seasons',
        'number_of_episodes',
        'episode_run_time',
        'status',
        'overview',
        'overview_translations',
        'tagline',
        'tagline_translations',
        'homepage',
        'popularity',
        'vote_average',
        'vote_count',
        'poster_path',
        'backdrop_path',
        'media_type',
        'adult',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_translations' => 'array',
        'overview_translations' => 'array',
        'tagline_translations' => 'array',
        'adult' => 'boolean',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
    ];

    public function localizedName(?string $locale = null): string
    {
        $translations = $this->name_translations;

        if (is_array($translations)) {
            $locale ??= app()->getLocale();

            if ($locale && isset($translations[$locale]) && is_string($translations[$locale]) && $translations[$locale] !== '') {
                return $translations[$locale];
            }

            $fallback = config('app.fallback_locale');

            if ($fallback && isset($translations[$fallback]) && is_string($translations[$fallback]) && $translations[$fallback] !== '') {
                return $translations[$fallback];
            }

            if (isset($translations['en']) && is_string($translations['en']) && $translations['en'] !== '') {
                return $translations['en'];
            }

            foreach ($translations as $value) {
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }
        }

        if (is_string($this->name) && $this->name !== '') {
            return $this->name;
        }

        return 'Untitled series';
    }

    public function localizedOverview(?string $locale = null): ?string
    {
        return $this->resolveLocalizedValue($this->overview_translations, $this->overview, $locale);
    }

    public function localizedTagline(?string $locale = null): ?string
    {
        return $this->resolveLocalizedValue($this->tagline_translations, $this->tagline, $locale);
    }

    /**
     * @param  array<string, mixed>|null  $translations
     */
    protected function resolveLocalizedValue($translations, ?string $fallbackValue, ?string $locale = null): ?string
    {
        if (is_array($translations)) {
            $locale ??= app()->getLocale();

            if ($locale && isset($translations[$locale]) && is_string($translations[$locale]) && $translations[$locale] !== '') {
                return $translations[$locale];
            }

            $fallback = config('app.fallback_locale');

            if ($fallback && isset($translations[$fallback]) && is_string($translations[$fallback]) && $translations[$fallback] !== '') {
                return $translations[$fallback];
            }

            if (isset($translations['en']) && is_string($translations['en']) && $translations['en'] !== '') {
                return $translations['en'];
            }

            foreach ($translations as $value) {
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }
        }

        return $fallbackValue;
    }

    /**
     * Users who have added this TV show to their watchlist.
     */
    public function watchlistedBy(): MorphToMany
    {
        return $this->morphToMany(User::class, 'watchlistable', 'user_watchlist')->withTimestamps();
    }

    /**
     * Watch history entries that include this show.
     */
    public function watchHistories(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'tv_show_person')
            ->withPivot(['credit_type', 'department', 'character', 'job', 'credit_order'])
            ->withTimestamps();
    }
}
