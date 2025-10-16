<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $last_confirmed_at
 */
class OmdbApiKey extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VALID = 'valid';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_UNKNOWN = 'unknown';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'status',
        'last_checked_at',
        'last_confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_checked_at' => 'datetime',
        'last_confirmed_at' => 'datetime',
    ];

    /**
     * Scope a query to only include candidate keys that still need verification.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where(function (Builder $builder): void {
            $builder->whereNull('status')->orWhere('status', self::STATUS_PENDING);
        });
    }
}
