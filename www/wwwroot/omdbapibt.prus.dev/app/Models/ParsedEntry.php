<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class ParsedEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'original_payload' => 'array',
        'parsed_payload' => 'array',
        'is_published' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function parseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ParsedEntryHistory::class);
    }

    public function diff(): array
    {
        $original = $this->original_payload ?? [];
        $parsed = $this->parsed_payload ?? [];

        $flattenedOriginal = Arr::dot($original);
        $flattenedParsed = Arr::dot($parsed);

        $added = [];
        $removed = [];
        $updated = [];

        foreach ($flattenedParsed as $key => $value) {
            if (! array_key_exists($key, $flattenedOriginal)) {
                $added[$key] = $value;
                continue;
            }

            if ($flattenedOriginal[$key] !== $value) {
                $updated[$key] = [
                    'from' => $flattenedOriginal[$key],
                    'to' => $value,
                ];
            }
        }

        foreach ($flattenedOriginal as $key => $value) {
            if (! array_key_exists($key, $flattenedParsed)) {
                $removed[$key] = $value;
            }
        }

        return [
            'added' => $added,
            'updated' => $updated,
            'removed' => $removed,
        ];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Pending',
        };
    }
}
