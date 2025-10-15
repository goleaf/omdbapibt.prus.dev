<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'overview' => 'array',
        'adult' => 'boolean',
        'video' => 'boolean',
        'release_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
    ];

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
}
