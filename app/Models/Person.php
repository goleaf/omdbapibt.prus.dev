<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];
}
