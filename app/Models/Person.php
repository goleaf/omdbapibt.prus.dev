<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'slug',
        'name',
        'biography',
        'birthday',
        'deathday',
        'place_of_birth',
        'gender',
        'known_for_department',
        'popularity',
        'profile_path',
    ];

    protected $casts = [
        'birthday' => 'date',
        'deathday' => 'date',
        'biography_translations' => 'array',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
            ->withPivot(['credit_type', 'department', 'character', 'job', 'credit_order'])
            ->withTimestamps();
    }

    public function tvShows(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class, 'tv_show_person')
            ->withPivot(['credit_type', 'department', 'character', 'job', 'credit_order'])
            ->withTimestamps();
    }
}
