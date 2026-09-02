<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the admin's notifications page, in the admin panel's own
     * layout — previously the notification bell's "Voir toutes" link sent
     * admins to /user/notifications, which (via ClientLayoutComposer, keyed
     * on user_type === 'client' — true for admin accounts too) rendered the
     * client-facing layout instead of staying in the admin panel.
     *
     * Marking as read / deleting reuse the existing user.notifications.*
     * routes — those are already scoped to Auth::id() and unrestricted by
     * role, so they work correctly for an admin without any change.
     */
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }
}
