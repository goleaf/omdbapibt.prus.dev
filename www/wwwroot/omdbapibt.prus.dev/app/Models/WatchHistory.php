<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'viewed_at' => 'datetime',
        'progress_percent' => 'integer',
    ];

    /**
     * The user that owns the viewing event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The watchable model (movie, tv show, etc.).
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }
}
