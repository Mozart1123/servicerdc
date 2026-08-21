<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the notifications page.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification): RedirectResponse|JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    /**
     * Get unread notifications count + new ones since a given timestamp.
     * Used by the real-time polling on the frontend.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $count  = Notification::where('user_id', $userId)->where('is_read', false)->count();

        $newNotifications = [];
        if ($request->filled('since')) {
            $since = \Carbon\Carbon::createFromTimestamp((int) $request->since);
            $newNotifications = Notification::where('user_id', $userId)
                ->where('created_at', '>', $since)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'type', 'title', 'message', 'action_url', 'is_read', 'created_at'])
                ->toArray();
        }

        return response()->json([
            'count'             => $count,
            'new_notifications' => $newNotifications,
        ]);
    }
}
