<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Display all active job offers with filters.
     */
    public function index(Request $request): View
    {
        $query = JobOffer::active()->notExpired();

        if ($request->filled('category'))      { $query->byCategory($request->category); }
        if ($request->filled('contract_type')) { $query->byContractType($request->contract_type); }
        if ($request->filled('location'))      { $query->byLocation($request->location); }
        if ($request->filled('search'))        { $query->search($request->search); }

        $jobs = $query->with('user', 'applications')
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        $contractTypes      = JobOffer::distinct()->pluck('contract_type')->filter()->values()->toArray();
        $userApplicationIds = Auth::user()->jobApplications()->pluck('job_offer_id')->toArray();

        return view('user.jobs.index', compact('jobs', 'contractTypes', 'userApplicationIds'));
    }

    /**
     * Display a single job offer.
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

        $userApplication = Auth::user()->jobApplications()->where('job_offer_id', $id)->first();
        $userCv          = Auth::user()->cv;

        return view('user.jobs.show', compact('job', 'relatedJobs', 'userApplication', 'userCv'));
    }

    public function apply(Request $request, JobOffer $job): RedirectResponse
    {
        $user = Auth::user();

        if (JobApplication::where('job_offer_id', $job->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        if (!$user->cv) {
            return redirect()->route('user.cv.index')->with('warning', 'Veuillez créer votre CV avant de postuler.');
        }

        $request->validate([
            'cv_attachment' => ['required', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:5120'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('cv_attachment')) {
            $attachmentPath = $request->file('cv_attachment')->store('cv_attachments', 'public');
        }

        $application = JobApplication::create([
            'job_offer_id'  => $job->id,
            'user_id'       => $user->id,
            'cv_id'         => $user->cv->id,
            'cover_letter'  => null,
            'cv_attachment' => $attachmentPath,
            'status'        => 'pending',
            'applied_at'    => now(),
        ]);

        $recruiterId = $job->employer_id ?? $job->user_id;
        if ($recruiterId) {
            Notification::create([
                'user_id'      => $recruiterId,
                'type'         => 'job_application',
                'related_type' => 'application',
                'related_id'   => $application->id,
                'title'        => 'Nouvelle candidature',
                'message'      => "{$user->name} a postulé à votre offre : {$job->title}",
                'action_url'   => route('user.applications.received'),
                'is_read'      => false,
            ]);
        }

        return back()->with('success', 'Votre candidature a été envoyée.');
    }

    /**
     * Client – My applications.
     */
    public function myApplications(Request $request): View
    {
        $user  = Auth::user();
        $query = $user->jobApplications()->with('jobOffer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10)->appends($request->query());

        $stats = [
            'total'     => $user->jobApplications()->count(),
            'pending'   => $user->jobApplications()->where('status', 'pending')->count(),
            'approved'  => $user->jobApplications()->whereIn('status', ['approved', 'accepted'])->count(),
            'rejected'  => $user->jobApplications()->where('status', 'rejected')->count(),
            'interview' => $user->jobApplications()->where('status', 'interview')->count(),
            'hired'     => $user->jobApplications()->where('status', 'hired')->count(),
        ];

        return view('user.applications.index', compact('applications', 'stats'));
    }

    /**
     * Withdraw a pending application.
     */
    public function withdrawApplication(int $applicationId): RedirectResponse
    {
        $application = JobApplication::findOrFail($applicationId);

        if (Auth::id() !== $application->user_id) {
            abort(403, 'Unauthorized');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Impossible de retirer une candidature déjà traitée.');
        }

        $application->delete();

        return back()->with('success', 'Candidature retirée avec succès.');
    }

    /**
     * Show the form to create a new job offer.
     */
    public function create(): View
    {
        return view('user.jobs.create');
    }

    /**
     * Show the form to edit an existing job offer.
     */
    public function editOffer(int $id): View
    {
        $job = JobOffer::findOrFail($id);
        if (Auth::id() !== $job->user_id && Auth::id() !== $job->employer_id) {
            abort(403);
        }
        return view('user.jobs.edit', compact('job'));
    }

    /**
     * Store a new job offer.
     */
    public function storeOffer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'company_name'  => ['required', 'string', 'max:255'],
            'category'      => ['required', 'string', 'max:100'],
            'location'      => ['required', 'string', 'max:100'],
            'contract_type' => ['required', 'string', 'max:50'],
            'description'   => ['required', 'string'],
            'requirements'  => ['nullable', 'string'],
            'company_logo'  => ['nullable', 'image', 'max:2048'],
            'cover_image'   => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $validated['user_id']     = $user->id;
        $validated['employer_id'] = $user->id;
        $validated['status']      = 'active';

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('job_images', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('job_images', 'public');
        }

        $job = JobOffer::create($validated);

        // Notify Admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->id,
                'type'         => 'job_created',
                'related_type' => 'job',
                'related_id'   => $job->id,
                'title'        => 'Nouvel emploi publié',
                'message'      => "{$user->name} a publié : {$job->title}",
                'action_url'   => route('admin.jobs.index'),
                'is_read'      => false,
            ]);
        }

        return redirect()->route('user.jobs.my-offers')->with('success', 'Offre publiée.');
    }

    /**
     * Update an existing job offer.
     */
    public function updateOffer(Request $request, int $id): RedirectResponse
    {
        $job = JobOffer::findOrFail($id);
        if (Auth::id() !== $job->user_id && Auth::id() !== $job->employer_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'company_name'  => ['required', 'string', 'max:255'],
            'category'      => ['required', 'string', 'max:100'],
            'location'      => ['required', 'string', 'max:100'],
            'contract_type' => ['required', 'string', 'max:50'],
            'status'        => ['required', 'in:active,closed'],
            'description'   => ['required', 'string'],
            'requirements'  => ['nullable', 'string'],
            'salary_range'  => ['nullable', 'string', 'max:100'],
            'deadline'      => ['nullable', 'date'],
            'company_logo'  => ['nullable', 'image', 'max:2048'],
            'cover_image'   => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('company_logo')) {
            if ($job->company_logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($job->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('job_images', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($job->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($job->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('job_images', 'public');
        }

        $job->update($validated);

        return redirect()->route('user.jobs.my-offers')->with('success', 'Offre mise à jour avec succès.');
    }

    /**
     * Delete a job offer.
     */
    public function destroyOffer(int $id): RedirectResponse
    {
        $job = JobOffer::findOrFail($id);
        if (Auth::id() !== $job->user_id && Auth::id() !== $job->employer_id) {
            abort(403);
        }

        $job->applications()->delete();
        $job->delete();

        return redirect()->route('user.jobs.my-offers')->with('success', 'Offre supprimée avec succès.');
    }

    public function approveApplication(int $applicationId): RedirectResponse
    {
        $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
        $user        = Auth::user();

        $application->update(['status' => 'approved']);

        Notification::create([
            'user_id'      => $application->user_id,
            'type'         => 'application_approved',
            'related_type' => 'application',
            'related_id'   => $application->id,
            'title'        => 'Candidature approuvée 🎉',
            'message'      => "Félicitations ! Votre candidature pour \"{$application->jobOffer->title}\" a été approuvée.",
            'action_url'   => route('user.applications.index'),
            'is_read'      => false,
        ]);

        Conversation::findOrCreateBetween($application->user_id, $user->id, 'job', $application->id);

        return back()->with('success', 'Candidature approuvée.');
    }

    public function rejectApplication(int $applicationId): RedirectResponse
    {
        $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
        $application->update(['status' => 'rejected']);

        Notification::create([
            'user_id'      => $application->user_id,
            'type'         => 'application_rejected',
            'related_type' => 'application',
            'related_id'   => $application->id,
            'title'        => 'Désolé',
            'message'      => "Votre candidature pour \"{$application->jobOffer->title}\" n'a pas été retenue.",
            'action_url'   => route('user.applications.index'),
            'is_read'      => false,
        ]);

        return back()->with('info', 'Candidature refusée.');
    }

    public function interviewApplication(int $applicationId): RedirectResponse
    {
        $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
        $user        = Auth::user();

        $application->update(['status' => 'interview']);

        Notification::create([
            'user_id'      => $application->user_id,
            'type'         => 'application_interview',
            'related_type' => 'application',
            'related_id'   => $application->id,
            'title'        => 'Entretien programmé',
            'message'      => "Vous êtes invité(e) à un entretien pour \"{$application->jobOffer->title}\".",
            'action_url'   => route('user.applications.index'),
            'is_read'      => false,
        ]);

        Conversation::findOrCreateBetween($application->user_id, $user->id, 'job', $application->id);

        return back()->with('success', 'Candidature marquée pour entretien.');
    }

    public function myJobOffers(): View
    {
        $user      = Auth::user();
        $jobOffers = $user->jobOffers()->withCount('applications')->latest()->paginate(10);
        return view('user.jobs.my-offers', compact('jobOffers'));
    }

    public function receivedApplications(Request $request): View
    {
        $user = Auth::user();
        $query = JobApplication::whereHas('jobOffer', function ($q) use ($user) {
            $q->where('employer_id', $user->id)->orWhere('user_id', $user->id);
        })->with('user', 'jobOffer', 'cv');
        
        $applications = $query->latest()->paginate(15);
        $stats = [
            'total'     => $query->count(),
            'pending'   => $query->where('status', 'pending')->count(),
            'approved'  => $query->whereIn('status', ['approved', 'accepted'])->count(),
            'rejected'  => $query->where('status', 'rejected')->count(),
            'interview' => $query->where('status', 'interview')->count(),
            'hired'     => $query->where('status', 'hired')->count(),
        ];
        return view('user.jobs.received-applications', compact('applications', 'stats'));
    }

    public function applicationDetails(int $id): \Illuminate\Http\JsonResponse
    {
        $application = JobApplication::with(['user.cv', 'jobOffer'])->findOrFail($id);
        return response()->json(['success' => true, 'application' => $application]);
    }
}
