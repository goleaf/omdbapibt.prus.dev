<?php

namespace App\Models;

use App\Enums\ParserEntryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ParserEntry extends Model
{
    use HasFactory;

    public const STATUS_PENDING = ParserEntryStatus::Pending->value;

    public const STATUS_APPROVED = ParserEntryStatus::Approved->value;

    public const STATUS_REJECTED = ParserEntryStatus::Rejected->value;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_type',
        'subject_id',
        'parser',
        'payload',
        'baseline_snapshot',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'baseline_snapshot' => 'array',
        'reviewed_at' => 'datetime',
        'status' => ParserEntryStatus::class,
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return HasMany<ParserEntryHistory>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ParserEntryHistory::class);
    }
}
