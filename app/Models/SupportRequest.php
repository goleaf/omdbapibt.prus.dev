<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
    ];
}
