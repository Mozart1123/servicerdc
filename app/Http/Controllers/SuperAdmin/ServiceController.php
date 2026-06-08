<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Super Admin Service Moderation Controller
 */
class ServiceController extends Controller
{
    /**
     * Display a listing of services for moderation.
     */
    public function index(Request $request): View
    {
        $query = Service::with(['artisan', 'category']);

        // Search by title or artisan name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhereHas('artisan', function ($aq) use ($request) {
                        $aq->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by verification
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $services = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.services.index', compact('services'));
    }

    /**
     * Toggle service verification status.
     */
    public function toggleVerification(Service $service): RedirectResponse
    {
        $service->update(['is_verified' => !$service->is_verified]);
        $status = $service->is_verified ? 'verified' : 'unverified';

        return redirect()->back()->with('success', "Service \"{$service->title}\" is now {$status}.");
    }

    /**
     * Toggle service status (Active/Suspended).
     */
    public function toggleStatus(Service $service): RedirectResponse
    {
        $newStatus = $service->status === 'active' ? 'suspended' : 'active';
        $service->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "Service \"{$service->title}\" status updated to {$newStatus}.");
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $title = $service->title;
        $service->delete();

        return redirect()->route('super-admin.services.index')
            ->with('success', "Service \"{$title}\" has been removed.");
    }
}
