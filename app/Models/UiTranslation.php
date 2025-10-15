<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $group
 * @property string $key
 * @property array<string, string> $value
 */
class UiTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\UiTranslationFactory> */
    use HasFactory;

    use HasTranslations;

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected array $translatable = ['value'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group')->orderBy('key');
    }
}
