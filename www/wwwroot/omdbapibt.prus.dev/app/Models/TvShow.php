<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvShow extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tv_shows';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'slug',
        'name',
        'original_name',
        'first_air_date',
        'last_air_date',
        'number_of_seasons',
        'number_of_episodes',
        'episode_run_time',
        'status',
        'overview',
        'tagline',
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
     * @var array<string, string>
     */
    protected $casts = [
        'adult' => 'boolean',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'popularity' => 'decimal:3',
        'vote_average' => 'decimal:1',
    ];

    public function watchlistedBy(): MorphToMany
    {
        return $this->morphToMany(User::class, 'watchable', 'user_watchlist')->withTimestamps();
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->name ?? $this->original_name ?? '';
    }
}
