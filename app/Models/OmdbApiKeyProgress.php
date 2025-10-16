<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmdbApiKeyProgress extends Model
{
    use HasFactory;

    protected $table = 'omdb_api_key_progress';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'sequence_cursor',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sequence_cursor' => 'string',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    public function scopeLatestCheckpoint(Builder $query): Builder
    {
        return $query->orderByDesc('updated_at');
    }
}
