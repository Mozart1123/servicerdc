<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'from_user_id',
        'mission_id',
        'type',
        'amount',
        'commission_amount',
        'net_amount',
        'status',
        'description',
        'reference_id',
    ];

    protected $casts = [
        'amount'            => 'float',
        'commission_amount' => 'float',
        'net_amount'        => 'float',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
