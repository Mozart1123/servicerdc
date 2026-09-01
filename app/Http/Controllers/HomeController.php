<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobCategory;
use App\Models\NewsletterSubscriber;
use App\Models\Service;
use App\Models\Category;
use App\Models\SupportTicket;
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
        $categories = Category::withCount(['services' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('created_at')->get();
        $jobCategories = JobCategory::withCount(['jobOffers' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('name')->get();

        return view('welcome', compact('categories', 'jobCategories'));
    }

    public function about(): View
    {
        return view('about');
    }

    public function contact(): View
    {
        return view('contact');
    }

    /**
     * Handle the public contact form submission.
     *
     * Creates a support ticket for the authenticated user. SupportTicket.user_id
     * is a required, non-nullable foreign key, so guests are asked to log in
     * first rather than allowing anonymous tickets (would need a schema change).
     */
    public function submitContact(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour nous envoyer un message.');
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        SupportTicket::create([
            'user_id'     => auth()->id(),
            'subject'     => $validated['subject'],
            'message'     => $validated['message'],
            'ticket_type' => 'general',
        ]);

        return back()->with('success', 'Merci ! Votre message a bien été envoyé, notre équipe vous répondra bientôt.');
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
