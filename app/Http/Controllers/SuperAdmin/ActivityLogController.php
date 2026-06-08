<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('super-admin.activity-logs.index', compact('logs'));
    }

    /**
     * Remove all logs.
     */
    public function clear()
    {
        ActivityLog::truncate();
        return redirect()->back()->with('success', 'Activity logs have been cleared.');
    }
}
