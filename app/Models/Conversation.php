<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'related_type',
        'related_id',
        // Legacy support
        'user_one',
        'user_two',
        'context',
        'context_id',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function userOne(): BelongsTo
    {
        $col = \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
        return $this->belongsTo(User::class, $col);
    }

    public function userTwo(): BelongsTo
    {
        $col = \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';
        return $this->belongsTo(User::class, $col);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Get the "other" participant for a given user.
     */
    public function otherUser(int $userId): ?User
    {
        $oneId = $this->user_one_id ?? $this->user_one;
        $twoId = $this->user_two_id ?? $this->user_two;

        if ($oneId == $userId) {
            return User::find($twoId);
        }
        return User::find($oneId);
    }

    /**
     * Count unread messages for a given user.
     */
    public function unreadCountFor(int $userId): int
    {
        return $this->messages()->where('sender_id', '!=', $userId)->where('is_read', false)->count();
    }

    /**
     * Find or create a conversation between two users.
     */
    public static function findOrCreateBetween(int $userA, int $userB, string $relatedType = null, int $relatedId = null): self
    {
        $oneCol = \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
        $twoCol = \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';

        $conv = self::where(function ($q) use ($userA, $userB, $oneCol, $twoCol) {
            $q->where($oneCol, $userA)->where($twoCol, $userB);
        })->orWhere(function ($q) use ($userA, $userB, $oneCol, $twoCol) {
            $q->where($oneCol, $userB)->where($twoCol, $userA);
        })->first();

        if (!$conv) {
            $conv = self::create([
                $oneCol       => $userA,
                $twoCol       => $userB,
                'related_type' => $relatedType ?? 'general',
                'related_id'   => $relatedId,
                'context'      => $relatedType ?? 'autre',
                'context_id'   => $relatedId,
            ]);
        }

        return $conv;
    }

    /**
     * Check if a user participates in this conversation.
     */
    public function hasParticipant(int $userId): bool
    {
        $oneId = $this->user_one_id ?? $this->user_one;
        $twoId = $this->user_two_id ?? $this->user_two;
        return $oneId == $userId || $twoId == $userId;
    }
}
