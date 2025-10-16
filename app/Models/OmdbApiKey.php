<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmdbApiKey extends Model
{
    use HasFactory;

    public const STATUS_WORKING = 'working';
    public const STATUS_DEAD = 'dead';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'first_seen_at',
        'last_checked_at',
        'last_response_code',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'first_seen_at' => 'immutable_datetime',
        'last_checked_at' => 'immutable_datetime',
    ];

    public function scopeWorking(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_WORKING);
    }

    public function scopeResumeFrom(Builder $query, ?string $cursor): Builder
    {
        if ($cursor === null) {
            return $query->orderBy('key');
        }

        return $query
            ->where('key', '>', $cursor)
            ->orderBy('key');
    }
}
