<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpayTransaction extends Model
{
    protected $fillable = [
        'mission_id',
        'transaction_ref',
        'phone_number',
        'amount',
        'status',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount'       => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the mission associated with this K-PAY transaction.
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter transactions by status.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
