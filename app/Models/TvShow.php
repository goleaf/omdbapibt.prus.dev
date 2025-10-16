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

    public function localizedName(?string $locale = null): string
    {
        return $this->localizedText('name_translations', $this->name, $locale, 'Untitled');
    }

    public function localizedOverview(?string $locale = null): ?string
    {
        return $this->localizedText('overview_translations', $this->overview, $locale);
    }

    public function localizedTagline(?string $locale = null): ?string
    {
        return $this->localizedText('tagline_translations', $this->tagline, $locale);
    }

    protected function localizedText(string $attribute, ?string $fallback, ?string $locale, ?string $default = null): ?string
    {
        $translations = $this->getAttribute($attribute);

        if (! is_array($translations) || empty($translations)) {
            return $this->stringOrDefault($fallback, $default);
        }

        $locale ??= app()->getLocale();

        if ($locale && $this->hasNonEmptyTranslation($translations, $locale)) {
            return $translations[$locale];
        }

        $fallbackLocale = config('app.fallback_locale');

        if ($fallbackLocale && $this->hasNonEmptyTranslation($translations, $fallbackLocale)) {
            return $translations[$fallbackLocale];
        }

        if ($this->isNonEmptyString($fallback)) {
            return $fallback;
        }

        foreach (['en', 'es', 'fr'] as $preferredLocale) {
            if ($this->hasNonEmptyTranslation($translations, $preferredLocale)) {
                return $translations[$preferredLocale];
            }
        }

        foreach ($translations as $value) {
            if ($this->isNonEmptyString($value)) {
                return $value;
            }
        }

        return $this->stringOrDefault($fallback, $default);
    }

    protected function hasNonEmptyTranslation(array $translations, string $locale): bool
    {
        return array_key_exists($locale, $translations) && $this->isNonEmptyString($translations[$locale]);
    }

    protected function isNonEmptyString(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    protected function stringOrDefault(?string $value, ?string $default): ?string
    {
        if ($this->isNonEmptyString($value)) {
            return $value;
        }

        return $default;
    }
}
