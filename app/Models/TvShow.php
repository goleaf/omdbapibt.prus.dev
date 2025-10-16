<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        $value = $this->resolveLocalizedValue(
            $this->name_translations,
            $locale,
            is_string($this->name) ? $this->name : null,
            'Untitled series'
        );

        return $value ?? 'Untitled series';
    }

    public function localizedOverview(?string $locale = null): ?string
    {
        return $this->resolveLocalizedValue(
            $this->overview_translations,
            $locale,
            is_string($this->overview) ? $this->overview : null
        );
    }

    public function localizedTagline(?string $locale = null): ?string
    {
        return $this->resolveLocalizedValue(
            $this->tagline_translations,
            $locale,
            is_string($this->tagline) ? $this->tagline : null
        );
    }

    public function scopeWhereLocalizedNameLike(Builder $query, string $term, ?string $locale = null): Builder
    {
        return static::applyLocalizedNameFilter($query, $term, $locale);
    }

    public static function applyLocalizedNameFilter(Builder $query, string $term, ?string $locale = null): Builder
    {
        $escaped = '%'.static::escapeLike($term).'%';
        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        return $query->where(function (Builder $innerQuery) use ($escaped, $locale, $fallbackLocale): void {
            $innerQuery
                ->where('name', 'like', $escaped)
                ->orWhere('original_name', 'like', $escaped)
                ->orWhere('slug', 'like', $escaped);

            if ($locale) {
                $innerQuery->orWhere("name_translations->{$locale}", 'like', $escaped);
            }

            if ($fallbackLocale && $fallbackLocale !== $locale) {
                $innerQuery->orWhere("name_translations->{$fallbackLocale}", 'like', $escaped);
            }
        });
    }

    public function setNameTranslationsAttribute($value): void
    {
        $this->storeTranslationAttribute('name_translations', $value);
    }

    public function setOverviewTranslationsAttribute($value): void
    {
        $this->storeTranslationAttribute('overview_translations', $value);
    }

    public function setTaglineTranslationsAttribute($value): void
    {
        $this->storeTranslationAttribute('tagline_translations', $value);
    }

    protected function storeTranslationAttribute(string $attribute, mixed $value): void
    {
        $normalized = $this->normalizeTranslations($value);

        if ($normalized === null) {
            $this->attributes[$attribute] = null;

            return;
        }

        $this->attributes[$attribute] = $this->castAttributeAsJson($attribute, $normalized);
    }

    protected function normalizeTranslations(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = ['en' => $value];
        }

        if (! is_array($value)) {
            return null;
        }

        $filtered = [];

        foreach ($value as $key => $text) {
            if (! is_string($key)) {
                continue;
            }

            if (! is_string($text)) {
                continue;
            }

            $trimmed = trim($text);

            if ($trimmed === '') {
                continue;
            }

            $filtered[$key] = $trimmed;
        }

        return $filtered === [] ? null : $filtered;
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

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'tv_show_genre')->withTimestamps();
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'tv_show_language')->withTimestamps();
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'tv_show_country')->withTimestamps();
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
