<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Movies saved in the user's watchlist.
     */
    public function watchlistMovies(): MorphToMany
    {
        return $this->morphedByMany(Movie::class, 'watchable', 'user_watchlist')->withTimestamps();
    }

    /**
     * TV shows saved in the user's watchlist.
     */
    public function watchlistTvShows(): MorphToMany
    {
        return $this->morphedByMany(TvShow::class, 'watchable', 'user_watchlist')->withTimestamps();
    }

    /**
     * Determine if the given media item is in the user's watchlist.
     */
    public function isInWatchlist(string $type, int $id): bool
    {
        $relation = $this->watchlistRelation($type);

        return $relation?->whereKey($id)->exists() ?? false;
    }

    /**
     * Add an item to the user's watchlist.
     */
    public function addToWatchlist(string $type, int $id): void
    {
        $relation = $this->watchlistRelation($type);

        if ($relation === null) {
            return;
        }

        $relation->syncWithoutDetaching([$id]);
    }

    /**
     * Remove an item from the user's watchlist.
     */
    public function removeFromWatchlist(string $type, int $id): void
    {
        $relation = $this->watchlistRelation($type);

        if ($relation === null) {
            return;
        }

        $relation->detach($id);
    }

    /**
     * Retrieve the appropriate watchlist relation for the given type.
     */
    protected function watchlistRelation(string $type): ?MorphToMany
    {
        return match ($type) {
            'movie' => $this->watchlistMovies(),
            'tv_show' => Schema::hasTable('tv_shows') ? $this->watchlistTvShows() : null,
            default => null,
        };
    }
}
