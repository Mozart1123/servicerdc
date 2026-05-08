<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Traits\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Super Admin Organization Controller
 */
class OrganizationController extends Controller
{
    use AuditLogger;

    /**
     * Display a listing of organizations.
     */
    public function index(Request $request): View
    {
        $query = Organization::with('owner');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $organizations = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('super-admin.organizations.create', compact('users'));
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,suspended',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        $organization = Organization::create($validated);

        $this->auditLog('created', "Created Organization: {$organization->name}", $organization, $validated);

        return redirect()->route('super-admin.organizations.index')
            ->with('success', 'Organization created successfully.');
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization): View
    {
        return view('super-admin.organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization): View
    {
        $users = User::orderBy('name')->get();
        return view('super-admin.organizations.edit', compact('organization', 'users'));
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,suspended',
            'description' => 'nullable|string',
        ]);

        if ($organization->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        }

        $oldData = $organization->toArray();
        $organization->update($validated);

        $this->auditLog('updated', "Updated Organization: {$organization->name}", $organization, [
            'old' => $oldData,
            'new' => $validated
        ]);

        return redirect()->route('super-admin.organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Toggle organization status.
     */
    public function toggleStatus(Organization $organization): RedirectResponse
    {
        $newStatus = $organization->status === 'active' ? 'suspended' : 'active';
        $oldStatus = $organization->status;
        $organization->update(['status' => $newStatus]);

        $this->auditLog('updated', "Toggled status for {$organization->name} to {$newStatus}", $organization, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ], 'warning');

        return redirect()->back()->with('success', "Organization {$organization->name} status updated to {$newStatus}.");
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $name = $organization->name;
        $oldData = $organization->toArray();
        $organization->delete();

        $this->auditLog('deleted', "Deleted Organization: {$name}", null, $oldData, 'danger');

        return redirect()->route('super-admin.organizations.index')
            ->with('success', "Organization {$name} has been deleted.");
    }
}
