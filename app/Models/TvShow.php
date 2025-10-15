<?php

namespace App\Models;

use App\Support\TmdbImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'adult' => 'boolean',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
    ];

    protected $appends = [
        'poster_image_url',
    ];

    /**
     * People who contributed to the TV show.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'person_tv_show')
            ->withPivot([
                'role',
                'character',
                'job',
                'department',
                'credit_order',
            ])
            ->withTimestamps()
            ->orderByPivot('credit_order');
    }

    /**
     * Poster image url accessor.
     */
    public function getPosterImageUrlAttribute(): string
    {
        return TmdbImage::poster($this->poster_path);
    }
}
