<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the super-admin's notifications page, in the super-admin
     * panel's own layout — previously there was no such page: the topbar
     * bell's "Voir toutes les notifications" link sent super-admins to
     * /user/notifications, which (via ClientLayoutComposer, keyed on
     * user_type === 'client' — true for super-admin accounts too) rendered
     * the client-facing layout instead of staying in the super-admin panel.
     * Mirrors the same fix already applied to the admin panel's own
     * NotificationController.
     *
     * Marking as read / deleting / marking all as read reuse the existing
     * user.notifications.* routes — those are already scoped to Auth::id()
     * and unrestricted by role, so they work correctly for a super-admin
     * without any change.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $base        = Notification::where('user_id', $userId);
        $total       = (clone $base)->count();
        $unreadTotal = (clone $base)->unread()->count();

        $query = Notification::where('user_id', $userId);
        if ($request->boolean('unread')) {
            $query->unread();
        }

        $notifications = $query
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('super-admin.notifications.index', compact('notifications', 'total', 'unreadTotal'));
    }
}
