<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'amount',
        'provider',
        'phone_number',
        'status',
        'reference_id',
        'kpay_reference',
        'notes',
    ];
}
