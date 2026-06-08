<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'price_yearly',
        'currency', 'features', 'color', 'icon', 'is_popular',
        'is_active', 'max_services', 'sort_order',
    ];

    protected $casts = [
        'features'        => 'array',
        'is_popular'      => 'boolean',
        'is_active'       => 'boolean',
        'price_monthly'   => 'decimal:2',
        'price_yearly'    => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
