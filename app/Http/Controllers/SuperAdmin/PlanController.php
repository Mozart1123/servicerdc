<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Traits\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlanController extends Controller
{
    use AuditLogger;

    /**
     * Display a listing of subscription plans.
     */
    public function index(): View
    {
        $plans = Plan::withCount('features')->latest()->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(): View
    {
        return view('super-admin.plans.create');
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'features.*.name' => 'required|string|max:255',
            'features.*.value' => 'nullable|string|max:255',
        ]);

        $plan = DB::transaction(function () use ($validated, $request) {
            $plan = Plan::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'billing_period' => $validated['billing_period'],
                'is_featured' => $request->has('is_featured'),
            ]);

            if (!empty($validated['features'])) {
                foreach ($validated['features'] as $featureData) {
                    $plan->features()->create($featureData);
                }
            }

            return $plan;
        });

        $this->auditLog('created', "Created Subscription Plan: {$plan->name}", $plan, $validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan created successfully with its features.');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan): View
    {
        $plan->load('features');
        return view('super-admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'features.*.id' => 'nullable|exists:plan_features,id',
            'features.*.name' => 'required|string|max:255',
            'features.*.value' => 'nullable|string|max:255',
        ]);

        $oldData = $plan->load('features')->toArray();

        DB::transaction(function () use ($validated, $plan, $request) {
            $plan->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'billing_period' => $validated['billing_period'],
                'is_featured' => $request->has('is_featured'),
            ]);

            // Sync features
            if (isset($validated['features'])) {
                $featureIds = collect($validated['features'])->pluck('id')->filter()->toArray();
                $plan->features()->whereNotIn('id', $featureIds)->delete();

                foreach ($validated['features'] as $featureData) {
                    if (isset($featureData['id'])) {
                        PlanFeature::where('id', $featureData['id'])->update([
                            'name' => $featureData['name'],
                            'value' => $featureData['value'],
                        ]);
                    } else {
                        $plan->features()->create($featureData);
                    }
                }
            } else {
                $plan->features()->delete();
            }
        });

        $this->auditLog('updated', "Updated Subscription Plan: {$plan->name}", $plan, [
            'old' => $oldData,
            'new' => $validated
        ]);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    /**
     * Toggle the status of a plan.
     */
    public function toggleStatus(Plan $plan): RedirectResponse
    {
        $oldStatus = $plan->is_active;
        $plan->update(['is_active' => !$plan->is_active]);
        $status = $plan->is_active ? 'activated' : 'deactivated';

        $this->auditLog('updated', "Toggled status for Plan \"{$plan->name}\" to {$status}", $plan, [
            'old_status' => $oldStatus,
            'new_status' => $plan->is_active
        ], 'warning');

        return redirect()->back()->with('success', "Plan \"{$plan->name}\" has been {$status}.");
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        $name = $plan->name;
        $oldData = $plan->load('features')->toArray();
        $plan->delete();

        $this->auditLog('deleted', "Deleted Subscription Plan: {$name}", null, $oldData, 'danger');

        return redirect()->route('super-admin.plans.index')
            ->with('success', "Plan \"{$name}\" has been deleted.");
    }
}
