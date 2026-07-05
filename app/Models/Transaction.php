<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'mission_id',
        'amount',
        'currency',
        'type',
        'status',
        'reference_id',
        'kpay_reference',
        'item_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
