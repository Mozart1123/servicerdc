<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'organization_id',
        'plan_id',
        'user_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'payment_method',
        'payment_phone',
        'transaction_ref',
        'amount_paid',
        'starts_at',
        'ends_at',
        'canceled_at',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'meta'          => 'array',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'canceled_at'   => 'datetime',
        'paid_at'       => 'datetime',
        'amount_paid'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // B2B Plan Relation
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    // B2C Plan Relation
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Dynamic Plan Accessor
    public function getPlanAttribute()
    {
        if ($this->subscription_plan_id) {
            return $this->subscriptionPlan;
        }
        return $this->getRelationValue('plan');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
