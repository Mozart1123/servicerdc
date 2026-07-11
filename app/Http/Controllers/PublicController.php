<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobOffer;
use App\Models\Service;
use App\Models\User;
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

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $jobs          = $query->with('user')->latest()->paginate(12)->appends($request->query());
        $contractTypes = JobOffer::where('status', 'active')->distinct()->pluck('contract_type')->filter()->values();

        return view('public.jobs.index', compact('jobs', 'contractTypes'));
    }

    /**
     * Public single job view.
     */
    public function jobShow(int $id): View
    {
        $job = JobOffer::with('user')->findOrFail($id);

        $related = JobOffer::where('status', 'active')
            ->where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.jobs.show', compact('job', 'related'));
    }

    /**
     * Public artisans directory.
     */
    public function artisans(Request $request): View
    {
        $query = User::where('user_type', 'artisan')->where('status', 'active');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('profession', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $artisans = $query->withCount('services')->latest()->paginate(12)->appends($request->query());

        return view('public.artisans.index', compact('artisans'));
    }

    /**
     * Public single artisan profile.
     */
    public function artisanShow(int $id): View
    {
        $artisan  = User::where('user_type', 'artisan')->with('services')->findOrFail($id);
        $services = $artisan->services()->where('status', 'active')->latest()->get();

        return view('public.artisans.show', compact('artisan', 'services'));
    }
}
