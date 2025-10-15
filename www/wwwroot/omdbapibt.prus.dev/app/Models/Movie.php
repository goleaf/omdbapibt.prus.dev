<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    protected $casts = [
        'adult' => 'boolean',
        'video' => 'boolean',
        'release_date' => 'date',
    ];
}
