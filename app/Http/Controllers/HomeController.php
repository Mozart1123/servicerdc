<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\NewsletterSubscriber;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): View
    {
        $recentJobs = JobOffer::where('status', 'active')->latest()->take(6)->get();
        $categories = Category::withCount(['services' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('created_at')->get();

        return view('welcome', compact('recentJobs', 'categories'));
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
        $user = auth()->user();
        if ($user && $user->user_type === \App\Models\User::TYPE_CLIENT && $user->role === \App\Models\User::ROLE_USER) {
            return redirect()->route('home');
        }
        return redirect()->route($user->dashboard_route);
    }

    /**
     * Store a new newsletter subscriber.
     */
    public function subscribeNewsletter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        NewsletterSubscriber::firstOrCreate([
            'email' => $validated['email'],
        ]);

        return back()->with('success', 'Merci ! Votre mail a bien été enregistré pour recevoir notre newsletter.');
    }
}
