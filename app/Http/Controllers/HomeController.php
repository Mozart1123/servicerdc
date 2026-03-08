<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): View
    {
        $recentJobs = JobOffer::where('status', 'active')->latest()->take(6)->get();
        $popularCategories = Service::select('profession')
            ->distinct()
            ->limit(12)
            ->get();

        return view('welcome', compact('recentJobs', 'popularCategories'));
    }

    public function about(): View
    {
        return view('about');
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function howItWorks(): View
    {
        return view('how-it-works');
    }

    public function dashboard()
    {
        return redirect()->route(auth()->user()->dashboard_route);
    }
}
