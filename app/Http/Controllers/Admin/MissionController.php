<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionController extends Controller
{
    /**
     * Display all missions with reviews for admin oversight.
     */
    public function index(Request $request): View
    {
        $query = Mission::with(['client', 'artisan', 'service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('artisan', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $missions = $query->paginate(20)->appends($request->query());

        $stats = [
            'total'       => Mission::count(),
            'in_progress' => Mission::inProgress()->count(),
            'completed'   => Mission::completed()->count(),
            'with_review' => Mission::whereNotNull('rating')->count(),
            'avg_rating'  => round(Mission::whereNotNull('rating')->avg('rating') ?? 0, 1),
        ];

        return view('admin.missions.index', compact('missions', 'stats'));
    }

    /**
     * Display a single mission with full review details.
     */
    public function show(Mission $mission): View
    {
        $mission->load(['client', 'artisan', 'service']);
        return view('admin.missions.show', compact('mission'));
    }
}
