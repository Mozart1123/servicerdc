<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * User Job Controller
 * 
 * Handles job-related functionality for job seekers and admins.
 */
class JobController extends Controller
{
    /**
     * Display all job offers with filters.
     */
    public function index(Request $request): View
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

        $contractTypes = ['CDD', 'CDI', 'Freelance', 'Stage'];
        $userApplicationIds = Auth::user()->jobApplications()->pluck('job_offer_id')->toArray();

        return view('user.jobs.index', compact('jobs', 'contractTypes', 'userApplicationIds'));
    }

    /**
     * Display job offer details.
     */
    public function show(int $id): View
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
    public function apply(Request $request, JobOffer $job): RedirectResponse
    {
        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:1000'],
            'resume_url' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        // Check if already applied
        $existingApplication = JobApplication::where('job_offer_id', $job->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        $resumePath = null;
        if ($request->hasFile('resume_url')) {
            $resumePath = $request->file('resume_url')->store('resumes', 'public');
        }

        $user = Auth::user();
        
        $application = JobApplication::create([
            'job_offer_id' => $job->id,
            'user_id' => $user->id,
            'cover_letter' => $request->cover_letter,
            'resume_url' => $resumePath,
            'message' => $request->message ?? $request->cover_letter,
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

        return back()->with('success', 'Votre candidature a été envoyée avec succès !');
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

    /**
     * Withdraw job application.
     */
    public function withdrawApplication(int $applicationId): RedirectResponse
    {
        $application = JobApplication::findOrFail($applicationId);
        
        if (Auth::id() !== $application->user_id) {
            abort(403, 'Unauthorized');
        }

        if ($application->status === 'pending') {
            $application->delete();
            return redirect()->back()->with('success', 'Candidature retirée.');
        }

        return redirect()->back()->with('error', 'Impossible de retirer une candidature traitée.');
    }
}
