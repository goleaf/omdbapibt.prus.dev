<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
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
        'omdb_id',
        'slug',
        'title',
        'original_title',
        'year',
        'runtime',
        'release_date',
        'overview',
        'plot',
        'tagline',
        'homepage',
        'budget',
        'revenue',
        'status',
        'popularity',
        'vote_average',
        'vote_count',
        'poster_path',
        'backdrop_path',
        'trailer_url',
        'translation_metadata',
        'credits',
        'streaming_links',
        'trailers',
        'media_type',
        'adult',
        'video',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title' => 'array',
        'overview' => 'array',
        'translation_metadata' => 'array',
        'credits' => 'array',
        'streaming_links' => 'array',
        'trailers' => 'array',
        'adult' => 'boolean',
        'video' => 'boolean',
        'release_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
    ];

    public function localizedTitle(?string $locale = null): string
    {
        $titles = $this->title;

        if (! is_array($titles)) {
            return is_string($titles) && $titles !== '' ? $titles : 'Untitled';
        }

        $locale ??= app()->getLocale();

        if ($locale && isset($titles[$locale]) && is_string($titles[$locale]) && $titles[$locale] !== '') {
            return $titles[$locale];
        }

        $fallbackLocale = config('app.fallback_locale');

        if ($fallbackLocale && isset($titles[$fallbackLocale]) && is_string($titles[$fallbackLocale]) && $titles[$fallbackLocale] !== '') {
            return $titles[$fallbackLocale];
        }

        if (isset($titles['en']) && is_string($titles['en']) && $titles['en'] !== '') {
            return $titles['en'];
        }

        foreach ($titles as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return 'Untitled';
    }

    public function setTitleAttribute($value): void
    {
        if (is_string($value)) {
            $value = ['en' => $value];
        }

        $this->attributes['title'] = $this->castAttributeAsJson('title', $value);
    }

    /**
     * Scope a query to filter by a minimum rating.
     */
    public function scopeWhereVoteAverageAtLeast(Builder $query, float $rating): Builder
    {
        return $query->where('vote_average', '>=', $rating);
    }

    /**
     * Genres associated with the movie.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre')->withTimestamps();
    }

    /**
     * Languages associated with the movie.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'movie_language')->withTimestamps();
    }

    /**
     * Countries associated with the movie.
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'movie_country')->withTimestamps();
    }

    /**
     * Users who have added this movie to their watchlist.
     */
    public function watchlistedBy(): MorphToMany
    {
        return $this->morphToMany(User::class, 'watchlistable', 'user_watchlist')->withTimestamps();
    }

    /**
     * Watch history entries that include this movie.
     */
    public function watchHistories(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'movie_person')
            ->withPivot(['credit_type', 'department', 'character', 'job', 'credit_order'])
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'movie_tag')->withTimestamps();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(ListModel::class, 'list_movie')->withTimestamps();
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'movie_platform')->withTimestamps();
    }

    public function requiresSubscription(): bool
    {
        $metadataRequirement = data_get($this->translation_metadata, 'access.requires_subscription');

        if (! is_null($metadataRequirement)) {
            return (bool) $metadataRequirement;
        }

        return (bool) data_get($this->streaming_links, 'requires_subscription', true);
    }
}
