<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'organization_id',
        'amount',
        'currency',
        'status',
        'reference_id',
        'description',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
