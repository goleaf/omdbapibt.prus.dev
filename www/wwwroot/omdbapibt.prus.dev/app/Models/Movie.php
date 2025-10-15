<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'omdb_id',
        'slug',
        'title',
        'original_title',
        'year',
        'runtime',
        'release_date',
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
        'adult' => 'boolean',
        'video' => 'boolean',
        'release_date' => 'date',
        'popularity' => 'decimal:3',
        'vote_average' => 'decimal:1',
    ];

    public function watchlistedBy(): MorphToMany
    {
        return $this->morphToMany(User::class, 'watchable', 'user_watchlist')->withTimestamps();
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?? '';
    }
}
