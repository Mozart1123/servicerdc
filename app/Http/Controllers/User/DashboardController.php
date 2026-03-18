<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * User Dashboard Controller
 * 
 * Handles user-specific functionality including services, jobs, and missions.
 */
class DashboardController extends Controller
{
    /**
     * Display the user dashboard overview.
     */
    public function index(): View
    {
        $user = Auth::user();
        $activeType = session('active_user_type', $user->user_type);

        $stats = [
            'applied_jobs_count' => $user->jobApplications()->count(),
            'total_jobs' => JobOffer::active()->count(),
            'total_services' => Service::active()->count(),
            'active_missions' => $user->missionsAsArtisan()->inProgress()->count() + 
                                $user->missionsAsClient()->inProgress()->count(),
            'unread_notifications' => $user->notifications()->unread()->count(),
        ];

        // Fetch recent job offers with proper eager loading
        $recentJobs = JobOffer::where('status', 'active')
                              ->where(function($q) {
                                  $q->where('deadline', '>=', now())
                                    ->orWhereNull('deadline');
                              })
                              ->with('user')
                              ->latest()
                              ->take(6)
                              ->get();
        
        $allJobs = JobOffer::where('status', 'active')
                          ->where(function($q) {
                              $q->where('deadline', '>=', now())
                                ->orWhereNull('deadline');
                          })
                          ->with('user')
                          ->latest()
                          ->get();
        
        $recentServices = Service::where('status', 'active')->latest()->take(6)->get();
        $categories = Category::all();
        $myApplications = $user->jobApplications()->with('jobOffer')->latest()->get();
        $notifications = $user->notifications()->latest()->take(5)->get();

        // Return view based on type
        $view = match($activeType) {
            'artisan' => 'user.artisan',
            'job_seeker' => 'user.emploie', 
            default => 'user.client',
        };

        return view($view, compact(
            'stats',
            'recentJobs',
            'allJobs',
            'recentServices',
            'categories',
            'myApplications',
            'notifications'
        ));
    }

    /**
     * Display user profile.
     */
    public function profile(): View
    {
        $user = Auth::user();
        
        $profileStats = [
            'total_jobs_applied' => $user->jobApplications()->count(),
            'jobs_accepted' => $user->jobApplications()->accepted()->count(),
            'missions_completed' => $user->missionsAsArtisan()->completed()->count() +
                                   $user->missionsAsClient()->completed()->count(),
            'average_rating' => $user->missionsAsArtisan()->whereNotNull('rating')->avg('rating') ?? 0,
        ];

        return view('user.profile', [
            'user' => $user,
            'profileStats' => $profileStats,
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'phone', 'bio', 'specialty', 'experience']));

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    // ==========================================
    // SERVICES SECTION
    // ==========================================

    /**
     * Display all services with filters.
     */
    public function services(Request $request): View
    {
        $query = Service::active()->verified();

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('location') && $request->location) {
            $query->byLocation($request->location);
        }

        $services = $query->with('category', 'artisan')
                         ->paginate(12)
                         ->appends($request->query());
        
        $categories = Category::all();

        return view('user.services.index', compact('services', 'categories'));
    }

    /**
     * Display service details.
     */
    public function serviceDetail(int $id): View
    {
        $service = Service::with('category', 'artisan', 'serviceRequests')
                         ->findOrFail($id);
        
        $relatedServices = Service::active()
                                 ->verified()
                                 ->where('category_id', $service->category_id)
                                 ->where('id', '!=', $id)
                                 ->take(4)
                                 ->get();

        return view('user.services.show', compact('service', 'relatedServices'));
    }

    // ==========================================
    // JOBS SECTION
    // ==========================================

    /**
     * Display all job offers with filters.
     */
    public function jobs(Request $request): View
    {
        $query = JobOffer::active()->notExpired();

        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        if ($request->has('contract_type') && $request->contract_type) {
            $query->byContractType($request->contract_type);
        }

        if ($request->has('location') && $request->location) {
            $query->byLocation($request->location);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $jobs = $query->with('user', 'applications')
                     ->latest()
                     ->paginate(12)
                     ->appends($request->query());

        $categories = Category::all();
        $contractTypes = ['CDD', 'CDI', 'Freelance', 'Stage'];
        $userApplicationIds = Auth::user()->jobApplications()->pluck('job_offer_id')->toArray();

        return view('user.jobs.index', compact('jobs', 'categories', 'contractTypes', 'userApplicationIds'));
    }

    /**
     * Display job offer details.
     */
    public function jobDetail(int $id): View
    {
        $job = JobOffer::with('user', 'applications')->findOrFail($id);
        $relatedJobs = JobOffer::active()
                              ->notExpired()
                              ->where('id', '!=', $id)
                              ->latest()
                              ->take(4)
                              ->get();

        $userApplication = Auth::user()->jobApplications()
                                       ->where('job_offer_id', $id)
                                       ->first();

        return view('user.jobs.show', compact('job', 'relatedJobs', 'userApplication'));
    }

    /**
     * Apply to a job offer.
     */
    public function applyToJob(Request $request, int $jobId): RedirectResponse
    {
        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:1000'],
            'resume_url' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        $job = JobOffer::findOrFail($jobId);
        $user = Auth::user();

        // Check if already applied
        $existingApplication = JobApplication::where('job_offer_id', $jobId)
                                             ->where('user_id', $user->id)
                                             ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        $resumePath = null;
        if ($request->hasFile('resume_url')) {
            $resumePath = $request->file('resume_url')->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'job_offer_id' => $jobId,
            'user_id' => $user->id,
            'cover_letter' => $request->cover_letter,
            'resume_url' => $resumePath,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // Create notification for admin
        Notification::create([
            'user_id' => $job->employer_id ?? 1,
            'type' => 'new_job_application',
            'title' => 'Nouvelle candidature',
            'message' => "{$user->name} a postulé à l'offre '{$job->title}'",
            'data' => [
                'job_id' => $job->id,
                'application_id' => $application->id,
                'applicant_id' => $user->id,
            ],
        ]);

        return redirect()->back()->with('success', 'Votre candidature a été envoyée avec succès.');
    }

    /**
     * Display user's job applications.
     */
    public function myApplications(): View
    {
        $user = Auth::user();
        $applications = $user->jobApplications()
                            ->with('jobOffer')
                            ->latest()
                            ->paginate(10);

        $stats = [
            'pending' => $user->jobApplications()->pending()->count(),
            'accepted' => $user->jobApplications()->accepted()->count(),
            'rejected' => $user->jobApplications()->rejected()->count(),
        ];

        return view('user.applications.index', compact('applications', 'stats'));
    }

    // ==========================================
    // MISSIONS SECTION
    // ==========================================

    /**
     * Display user's missions (both as client and artisan).
     */
    public function missions(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $clientMissions = $user->missionsAsClient();
        $artisanMissions = $user->missionsAsArtisan();

        if ($status) {
            $clientMissions->where('status', $status);
            $artisanMissions->where('status', $status);
        }

        $clientMissions = $clientMissions->with('artisan', 'service')->latest()->get();
        $artisanMissions = $artisanMissions->with('client', 'service')->latest()->get();

        $stats = [
            'in_progress' => $user->missionsAsArtisan()->inProgress()->count() +
                            $user->missionsAsClient()->inProgress()->count(),
            'completed' => $user->missionsAsArtisan()->completed()->count() +
                          $user->missionsAsClient()->completed()->count(),
            'cancelled' => $user->missionsAsArtisan()->cancelled()->count() +
                          $user->missionsAsClient()->cancelled()->count(),
        ];

        return view('user.missions.index', compact('clientMissions', 'artisanMissions', 'stats'));
    }

    /**
     * Display mission details.
     */
    public function missionDetail(int $id): View
    {
        $mission = Mission::with('client', 'artisan', 'service')->findOrFail($id);
        
        // Check authorization
        if (Auth::id() !== $mission->client_id && Auth::id() !== $mission->artisan_id) {
            abort(403, 'Unauthorized');
        }

        return view('user.missions.show', compact('mission'));
    }

    /**
     * Update mission status.
     */
    public function updateMissionStatus(Request $request, int $missionId): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'feedback' => ['nullable', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $mission = Mission::findOrFail($missionId);
        
        // Check authorization
        if (Auth::id() !== $mission->client_id && Auth::id() !== $mission->artisan_id) {
            abort(403, 'Unauthorized');
        }

        $mission->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'rating' => $request->rating,
        ]);

        return redirect()->back()->with('success', 'Statut de la mission mis à jour.');
    }

    /**
     * Display notifications.
     */
    public function notifications(): View
    {
        $user = Auth::user();
        $notifications = $user->notifications()
                             ->latest()
                             ->paginate(10);

        return view('user.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationAsRead(int $notificationId): RedirectResponse
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();

        return redirect()->back();
    }

    /**
     * Display the Help Center / Service Request page.
     */
    public function help(): View
    {
        $user = Auth::user();
        $latestRequests = $user->serviceRequests()
                              ->latest()
                              ->limit(5)
                              ->get();

        return view('user.help', compact('latestRequests'));
    }

    /**
     * Display the Report a Problem page.
     */
    public function report(): View
    {
        return view('user.report');
    }

    /**
     * Display the New Opportunities page.
     */
    public function newOpportunities(): View
    {
        return view('user.new');
    }

    /**
     * Display the User's Favorites page.
     */
    public function favorites(): View
    {
        return view('user.favorites');
    }

    /**
     * Display the Security page.
     */
    public function security(): View
    {
        return view('user.security');
    }
}

