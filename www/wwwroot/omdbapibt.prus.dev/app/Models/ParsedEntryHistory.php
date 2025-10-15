<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParsedEntryHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ParsedEntry::class, 'parsed_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
