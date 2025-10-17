<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable;

    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'preferred_locale',
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
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function roleLabel(): string
    {
        return $this->role instanceof UserRole
            ? $this->role->label()
            : UserRole::User->label();
    }

    public function canImpersonate(): bool
    {
        return $this->role instanceof UserRole
            ? $this->role->canImpersonate()
            : false;
    }

    public function canBeImpersonated(): bool
    {
        return $this->role instanceof UserRole
            ? $this->role->canBeImpersonated()
            : true;
    }

    /**
     * Ratings authored by the user.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Ratings where the user liked the movie.
     */
    public function likedRatings(): HasMany
    {
        return $this->ratings()->liked();
    }

    /**
     * Ratings where the user disliked the movie.
     */
    public function dislikedRatings(): HasMany
    {
        return $this->ratings()->disliked();
    }

    /**
     * Reviews authored by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Lists created by the user.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ListModel::class);
    }

    /**
     * Users this account is following.
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'follower_id', 'followed_id')
            ->using(Follow::class)
            ->withTimestamps();
    }

    /**
     * Users following this account.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follows', 'followed_id', 'follower_id')
            ->using(Follow::class)
            ->withTimestamps();
    }

    /**
     * Retrieve the default "watch later" list, creating it when necessary.
     */
    public function ensureWatchLaterList(): ListModel
    {
        return $this->lists()->firstOrCreate(
            ['title' => ListModel::WATCH_LATER_TITLE],
            [
                'public' => false,
                'description' => null,
                'cover_url' => null,
            ],
        );
    }

    /**
     * Determine if the default "watch later" list includes the movie.
     */
    public function hasInWatchLater(Movie $movie): bool
    {
        $list = $this->lists()->watchLater()->first();

        if (! $list) {
            return false;
        }

        return $list->items()->where('movie_id', $movie->getKey())->exists();
    }

    /**
     * Watch history entries associated with the user.
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function managementLogs(): HasMany
    {
        return $this->hasMany(UserManagementLog::class);
    }

    public function actedManagementLogs(): HasMany
    {
        return $this->hasMany(UserManagementLog::class, 'actor_id');
    }

    public function hasPremiumAccess(string $subscription = 'default'): bool
    {
        if ($this->subscribed($subscription)) {
            return true;
        }

        if ($this->onTrial($subscription)) {
            return true;
        }

        $subscriptionModel = $this->subscription($subscription);

        return (bool) ($subscriptionModel?->onGracePeriod());
    }

    public function canAccessBillingPortal(string $subscription = 'default'): bool
    {
        if (! $this->hasStripeId()) {
            return false;
        }

        $subscriptionModel = $this->subscription($subscription);

        if (! $subscriptionModel) {
            return false;
        }

        return $subscriptionModel->valid()
            || $subscriptionModel->onGracePeriod()
            || $subscriptionModel->onTrial();
    }
}
