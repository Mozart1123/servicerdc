<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\Service;
use App\Models\User;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Public services catalog.
     */
    public function services(Request $request): View
    {
        $query = Service::where('status', 'active');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $services   = $query->with('artisan', 'category')->latest()->paginate(12)->appends($request->query());
        $categories = Category::all();

        return view('public.services.index', compact('services', 'categories'));
    }

    /**
     * Public single service view.
     */
    public function serviceShow(int $id): View
    {
        $service = Service::with('artisan', 'category')->findOrFail($id);

        $related = Service::where('status', 'active')
            ->where('id', '!=', $id)
            ->where('category_id', $service->category_id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.services.show', compact('service', 'related'));
    }

    /**
     * Public jobs catalog.
     */
    public function jobs(Request $request): View
    {
        $query = JobOffer::where('status', 'active')
            ->where(function ($q) {
                $q->where('deadline', '>=', now())->orWhereNull('deadline');
            });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->job_category_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $jobs          = $query->with('user')->latest()->paginate(12)->appends($request->query());
        $contractTypes = JobOffer::where('status', 'active')->distinct()->pluck('contract_type')->filter()->values();
        $jobCategories = JobCategory::whereHas('jobOffers', function ($q) {
            $q->where('status', 'active');
        })->orderBy('name')->get();

        return view('public.jobs.index', compact('jobs', 'contractTypes', 'jobCategories'));
    }

    /**
     * Public single job view.
     */
    public function jobShow(int $id): View
    {
        $job = JobOffer::with(['user', 'applications', 'employer'])->findOrFail($id);

        $related = JobOffer::where('status', 'active')
            ->where(function ($q) { $q->where('deadline', '>=', now())->orWhereNull('deadline'); })
            ->where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.jobs.show', compact('job', 'related'));
    }

    /**
     * Apply redirect: sets intended URL then redirects to login (guests)
     * or directly to the apply form (authenticated users).
     * Uses Laravel's built-in intended() — no open redirect possible.
     */
    public function jobApplyRedirect(int $id): \Illuminate\Http\RedirectResponse
    {
        $job = JobOffer::findOrFail($id);

        if (\Illuminate\Support\Facades\Auth::check()) {
            // Already authenticated — go straight to the apply form
            return redirect()->route('user.jobs.apply.form', $job->id);
        }

        // Store the intended destination (the apply form URL) in session
        // redirect()->guest() is Laravel's canonical secure way to handle this
        return redirect()->guest(route('user.jobs.apply.form', $job->id));
    }

    /**
     * Public artisans directory.
     */
    public function artisans(Request $request): View
    {
        $query = User::where('user_type', 'artisan')->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('bio', 'like', '%' . $search . '%')
                  ->orWhereHas('services', function ($sq) use ($search) {
                      $sq->where('profession', 'like', '%' . $search . '%')
                         ->orWhere('title', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $artisans = $query->with(['artisanLevel'])
            ->withCount('services')
            ->leftJoin('artisan_levels', 'users.id', '=', 'artisan_levels.user_id')
            ->orderByRaw("FIELD(artisan_levels.level, 'nouveau', 'actif', 'verifie', 'elite') DESC")
            ->latest('users.created_at')
            ->select('users.*') // Ensure we only select user columns
            ->paginate(12)
            ->appends($request->query());

        return view('public.artisans.index', compact('artisans'));
    }

    /**
     * Public single artisan profile.
     */
    public function artisanShow(int $id): View
    {
        $artisan  = User::where('user_type', 'artisan')->with(['services', 'artisanLevel'])->findOrFail($id);
        $services = $artisan->services()->where('status', 'active')->latest()->get();

        return view('public.artisans.show', compact('artisan', 'services'));
    }

    /**
     * Display service types (sub-services) for a specific category.
     */
    public function categoryServiceTypes(Category $category): View
    {
        $serviceTypes = $category->serviceTypes()->withCount(['services' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        return view('public.categories.service-types', compact('category', 'serviceTypes'));
    }

    /**
     * Display artisan service offers filtered by a specific service type.
     */
    public function serviceTypeServices(ServiceType $serviceType): View
    {
        $services = $serviceType->services()
            ->where('status', 'active')
            ->with(['artisan', 'category'])
            ->latest()
            ->paginate(12);

        return view('public.service-types.services', compact('serviceType', 'services'));
    }

    /**
     * API endpoint returning service types for a category.
     */
    public function apiCategoryServiceTypes(Category $category): \Illuminate\Http\JsonResponse
    {
        return response()->json($category->serviceTypes);
    }
}
