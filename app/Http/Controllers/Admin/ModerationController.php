<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    /**
     * Display pending reviews for moderation.
     */
    public function reviews(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $reviews = Review::query()
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->with('client', 'artisan', 'mission')
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'  => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'rejected' => Review::rejected()->count(),
            'total'    => Review::count(),
            'avg_rating' => Review::avg('rating') ?? 0,
        ];

        return view('admin.moderation.reviews', compact('reviews', 'stats', 'status'));
    }

    /**
     * Approve a review.
     */
    public function approveReview(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved']);

        if ($review->artisan_id) {
            Notification::create([
                'user_id'    => $review->artisan_id,
                'type'       => 'review_approved',
                'title'      => 'Avis approuvé',
                'message'    => "Votre avis de {$review->rating}/5 étoiles a été approuvé.",
                'action_url' => route('user.artisan.reviews.index'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Avis approuvé avec succès.');
    }

    /**
     * Reject a review.
     */
    public function rejectReview(Request $request, int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('reason', ''),
        ]);

        return back()->with('success', 'Avis rejeté.');
    }
}
