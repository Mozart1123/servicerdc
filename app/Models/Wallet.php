<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'currency',
    ];

    protected $casts = [
        'balance'         => 'float',
        'pending_balance' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    /**
     * Credit the wallet balance.
     */
    public function credit(float $amount, float $commissionAmount = 0.0, ?int $fromUserId = null, ?int $missionId = null, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        $netAmount = max(0, $amount - $commissionAmount);

        $this->increment('balance', $netAmount);

        return $this->transactions()->create([
            'user_id'           => $this->user_id,
            'from_user_id'      => $fromUserId,
            'mission_id'        => $missionId,
            'type'              => 'credit',
            'amount'            => $amount,
            'commission_amount' => $commissionAmount,
            'net_amount'        => $netAmount,
            'status'            => 'completed',
            'description'       => $description ?? 'Paiement reçu pour service',
            'reference_id'      => $referenceId,
        ]);
    }

    /**
     * Debit the wallet balance for a payout/withdrawal.
     */
    public function debit(float $amount, ?string $description = null, ?string $referenceId = null): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Solde insuffisant dans le portefeuille.');
        }

        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'user_id'           => $this->user_id,
            'from_user_id'      => null,
            'mission_id'        => null,
            'type'              => 'debit',
            'amount'            => $amount,
            'commission_amount' => 0.0,
            'net_amount'        => $amount,
            'status'            => 'completed',
            'description'       => $description ?? 'Retrait vers Mobile Money',
            'reference_id'      => $referenceId,
        ]);
    }
}
