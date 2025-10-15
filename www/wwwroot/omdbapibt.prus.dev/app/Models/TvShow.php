<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvShow extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    protected $casts = [
        'adult' => 'boolean',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
    ];
}
