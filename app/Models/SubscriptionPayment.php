<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as CashierSubscription;

class SubscriptionPayment extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'invoice_id',
        'invoice_number',
        'paid_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(CashierSubscription::class);
    }

    public function formattedAmount(): string
    {
        $amount = number_format($this->amount / 100, 2);

        return sprintf('%s %s', $amount, Str::upper($this->currency));
    }

    public function statusLabel(): string
    {
        return Str::headline($this->status);
    }
}
