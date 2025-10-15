<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'watchable_type',
        'watchable_id',
        'progress_percent',
        'completed',
        'watched_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'watched_at' => 'datetime',
        'completed' => 'boolean',
        'progress_percent' => 'integer',
    ];

    /**
     * The user who watched the media item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The media that was watched.
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Human readable label for the watchable type.
     */
    public function mediaTypeLabel(): string
    {
        return match ($this->watchable_type) {
            Movie::class => 'Movie',
            TvShow::class => 'TV Show',
            default => 'Media',
        };
    }
}
