<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'bio',
        'home_country_id',
        'primary_genre_id',
        'secondary_genre_id',
        'favorite_movie_id',
        'favorite_tv_show_id',
        'favorite_person_id',
        'primary_language_id',
        'secondary_language_id',
        'subtitle_language_id',
        'weekly_watch_minutes',
        'average_session_minutes',
        'preferred_watch_hour',
        'binge_watch_score',
        'rewatch_affinity',
        'last_watched_at',
        'recent_watch_highlights',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_watched_at' => 'datetime',
            'recent_watch_highlights' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'home_country_id');
    }

    public function primaryGenre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'primary_genre_id');
    }

    public function secondaryGenre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'secondary_genre_id');
    }

    public function favoriteMovie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'favorite_movie_id');
    }

    public function favoriteTvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class, 'favorite_tv_show_id');
    }

    public function favoritePerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'favorite_person_id');
    }

    public function primaryLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'primary_language_id');
    }

    public function secondaryLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'secondary_language_id');
    }

    public function subtitleLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'subtitle_language_id');
    }

    public function genrePreferences(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'user_profile_genre_preferences')
            ->withPivot(['preference_rank', 'preference_score'])
            ->withTimestamps()
            ->orderByPivot('preference_rank');
    }

    public function languagePreferences(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'user_profile_language_preferences')
            ->withPivot(['preference_type', 'preference_rank'])
            ->withTimestamps()
            ->orderByPivot('preference_rank');
    }

    public function favoritePeople(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'user_profile_person_favorites')
            ->withPivot(['preference_rank', 'affinity_reason'])
            ->withTimestamps()
            ->orderByPivot('preference_rank');
    }
}
