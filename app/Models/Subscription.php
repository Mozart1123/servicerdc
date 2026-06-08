<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'subscription_plan_id', 'status', 'billing_cycle',
        'payment_method', 'payment_phone', 'transaction_ref',
        'amount_paid', 'starts_at', 'ends_at', 'paid_at', 'meta',
    ];

    protected $casts = [
        'meta'       => 'array',
        'starts_at'  => 'date',
        'ends_at'    => 'date',
        'paid_at'    => 'datetime',
        'amount_paid'=> 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
