<?php

namespace App\Models;

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
        'slug',
        'name',
        'original_name',
        'first_air_date',
        'last_air_date',
        'overview',
        'number_of_seasons',
        'number_of_episodes',
        'status',
        'popularity',
        'vote_average',
        'vote_count',
        'poster_path',
        'backdrop_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'popularity' => 'float',
        'vote_average' => 'float',
    ];

    /**
     * Retrieve the people associated with the TV show.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'tv_show_person')
            ->withPivot(['role', 'department', 'character', 'job', 'order'])
            ->withTimestamps();
    }
}
