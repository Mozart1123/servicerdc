<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * When a review is approved, sync rating/feedback to the parent Mission
     * so Mission.rating acts as a read-through cache.
     */
    public function updated(Review $review): void
    {
        // Only act when transitioning to 'approved'
        if ($review->wasChanged('status') && $review->status === 'approved' && $review->mission_id) {
            $review->mission?->update([
                'rating'   => $review->rating,
                'feedback' => $review->feedback,
            ]);
        }
    }
}
