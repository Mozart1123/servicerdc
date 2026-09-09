<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'related_type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'related_id',
        'action_url',
    ];

    protected $casts = [
        'data'    => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function markAsRead(): static
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
        return $this;
    }

    public function markAsUnread(): static
    {
        $this->update(['is_read' => false, 'read_at' => null]);
        return $this;
    }

    // ─── Presentation helpers ────────────────────────────────────────────────
    //
    // The `type` column is a free-form slug written by ~40 different call
    // sites across the app (NotificationService, webhooks, admin actions,
    // console commands...) with no shared enum — e.g. 'service_request',
    // 'mission_payment_confirmed', 'identity_rejected', 'payout_approved'.
    // Rather than maintaining an exhaustive 1:1 map (which silently breaks
    // every time a new type string is introduced somewhere), these bucket
    // any type into one of a handful of stable presentation categories by
    // keyword, with a safe neutral fallback for anything unrecognised.

    private const CATEGORY_ICONS = [
        'negative'     => ['bg-red-50', 'text-red-500', 'fa-circle-xmark'],
        'review'       => ['bg-amber-50', 'text-amber-600', 'fa-star'],
        'verification' => ['bg-indigo-50', 'text-indigo-600', 'fa-shield-halved'],
        'message'      => ['bg-cyan-50', 'text-cyan-600', 'fa-comment-alt'],
        'payment'      => ['bg-emerald-50', 'text-emerald-600', 'fa-credit-card'],
        'request'      => ['bg-blue-50', 'text-blue-600', 'fa-briefcase'],
        'default'      => ['bg-slate-50', 'text-slate-600', 'fa-info-circle'],
    ];

    private const CATEGORY_LABELS = [
        'negative'     => 'Refus',
        'review'       => 'Avis',
        'verification' => 'Vérification',
        'message'      => 'Message',
        'payment'      => 'Paiement',
        'request'      => 'Activité',
        'default'      => 'Information',
    ];

    /**
     * Bucket this notification's raw `type` string into a stable
     * presentation category, by keyword — see the block comment above.
     */
    public function category(): string
    {
        $type = mb_strtolower((string) $this->type);

        if (str_contains($type, 'reject') || str_contains($type, 'refus') || str_contains($type, 'fail') || str_contains($type, 'cancel')) {
            return 'negative';
        }

        if (str_contains($type, 'review') || str_contains($type, 'rated') || str_contains($type, 'rating')) {
            return 'review';
        }

        if (str_contains($type, 'verif') || str_contains($type, 'identit')) {
            return 'verification';
        }

        if (str_contains($type, 'message')) {
            return 'message';
        }

        if (
            str_contains($type, 'payment') || str_contains($type, 'payout') || str_contains($type, 'subscription')
            || str_contains($type, 'commission') || str_contains($type, 'refund') || str_contains($type, 'paid')
            || in_array($type, ['credit', 'debit'], true)
        ) {
            return 'payment';
        }

        if (
            str_contains($type, 'application') || str_contains($type, 'job') || str_contains($type, 'mission')
            || str_contains($type, 'service') || str_contains($type, 'account')
        ) {
            return 'request';
        }

        return 'default';
    }

    /**
     * Short French label for this notification's category, used as the
     * eyebrow text in the detail view.
     */
    public function categoryLabel(): string
    {
        return self::CATEGORY_LABELS[$this->category()] ?? self::CATEGORY_LABELS['default'];
    }

    /**
     * [background class, text/icon class, Font Awesome icon class] for
     * this notification's category.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    public function iconClasses(): array
    {
        return self::CATEGORY_ICONS[$this->category()] ?? self::CATEGORY_ICONS['default'];
    }
}
