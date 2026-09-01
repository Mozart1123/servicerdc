<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Display all active job offers with filters.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $applications = $user->jobApplications()
            ->with('jobOffer')
            ->latest()
            ->paginate(10);

        return view('user.jobs.index', compact('applications'));
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

        return view('user.jobs.show', compact('job', 'relatedJobs', 'userApplication'));
    }

    public function showApplyForm(int $id): View|RedirectResponse
    {
        $job = JobOffer::with('user')->findOrFail($id);
        $user = Auth::user();

        if (JobApplication::where('job_offer_id', $job->id)->where('user_id', $user->id)->exists()) {
            return redirect()->route('user.applications.index')->with('info', 'Vous avez déjà postulé à cette offre.');
        }

        return view('user.jobs.apply', compact('job', 'user'));
    }

    public function apply(Request $request, JobOffer $job): RedirectResponse
    {
        $user = Auth::user();

        if (JobApplication::where('job_offer_id', $job->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        $validated = $request->validate([
            'cv_attachment' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'message'       => ['nullable', 'string', 'max:2000'],
        ]);

        $cvPath = $request->file('cv_attachment')->store('job_applications/cvs', 'public');

        $application = JobApplication::create([
            'job_offer_id'    => $job->id,
            'user_id'         => $user->id,
            'cv_id'           => $user->cv ? $user->cv->id : null, // Optional fallback reference
            'applicant_name'  => $user->name,
            'applicant_email' => $user->email,
            'applicant_phone' => $user->phone ?? null,
            'message'         => $validated['message'] ?? null,
            'cv_attachment'   => $cvPath,
            'status'          => JobApplication::STATUS_PENDING,
            'applied_at'      => now(),
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

        return redirect()->route('user.applications.index')->with('success', 'Votre candidature a été envoyée.');
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
        $jobCategories = JobCategory::orderBy('name')->get();
        return view('user.jobs.create', compact('jobCategories'));
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
        $jobCategories = JobCategory::orderBy('name')->get();
        return view('user.jobs.edit', compact('job', 'jobCategories'));
    }

    /**
     * Store a new job offer.
     */
    public function storeOffer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'company_name'     => ['required', 'string', 'max:255'],
            'job_category_id'  => ['required', 'integer', 'exists:job_categories,id'],
            'location'         => ['required', 'string', 'max:100'],
            'contract_type'    => ['required', 'string', 'max:50'],
            'description'      => ['required', 'string'],
            'requirements'     => ['nullable', 'string'],
            'company_logo'     => ['nullable', 'image', 'max:5120'],
            'cover_image'      => ['nullable', 'image', 'max:5120'],
            'is_urgent'        => ['nullable', 'boolean'],
        ]);

        try {
            $user = Auth::user();

            if (!$user->isPremiumRecruiter() && JobOffer::where('employer_id', $user->id)->active()->count() >= 3) {
                return back()->with('error', 'Vous avez atteint la limite de 3 offres actives. Passez au plan Premium pour publier des offres illimitées.')
                             ->with('upgrade_url', route('user.subscription.index'))
                             ->withInput();
            }

            // Derive the legacy `category` text from the chosen structured category
            // so every existing view/search/filter reading `$job->category` keeps working.
            $validated['category']    = JobCategory::findOrFail($validated['job_category_id'])->name;
            $validated['user_id']     = $user->id;
            $validated['employer_id'] = $user->id;
            $validated['status']      = 'active';
            $validated['is_urgent']   = $request->boolean('is_urgent') && $user->isPremiumRecruiter();

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

            return redirect()->route('user.jobs.my-offers')->with('success', 'Offre publiée avec succès.');
        } catch (\Throwable $e) {
            \Log::error('Erreur publication offre: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Une erreur est survenue lors de la publication de l\'offre.')->withInput();
        }
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
            'title'           => ['required', 'string', 'max:255'],
            'company_name'    => ['required', 'string', 'max:255'],
            'job_category_id' => ['required', 'integer', 'exists:job_categories,id'],
            'location'        => ['required', 'string', 'max:100'],
            'contract_type'   => ['required', 'string', 'max:50'],
            'status'          => ['required', 'in:active,closed'],
            'description'     => ['required', 'string'],
            'requirements'    => ['nullable', 'string'],
            'salary_range'    => ['nullable', 'string', 'max:100'],
            'deadline'        => ['nullable', 'date'],
            'company_logo'    => ['nullable', 'image', 'max:5120'],
            'cover_image'     => ['nullable', 'image', 'max:5120'],
            'is_urgent'       => ['nullable', 'boolean'],
        ]);

        try {
            // Derive the legacy `category` text from the chosen structured category
            // so every existing view/search/filter reading `$job->category` keeps working.
            $validated['category']  = JobCategory::findOrFail($validated['job_category_id'])->name;
            $validated['is_urgent'] = $request->boolean('is_urgent') && Auth::user()->isPremiumRecruiter();

            if ($request->hasFile('company_logo')) {
                if ($job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                $validated['company_logo'] = $request->file('company_logo')->store('job_images', 'public');
            }

            if ($request->hasFile('cover_image')) {
                if ($job->cover_image) {
                    Storage::disk('public')->delete($job->cover_image);
                }
                $validated['cover_image'] = $request->file('cover_image')->store('job_images', 'public');
            }

            $job->update($validated);

            return redirect()->route('user.jobs.my-offers')->with('success', 'Offre mise à jour avec succès.');
        } catch (\Throwable $e) {
            \Log::error('Erreur mise à jour offre: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'offre.')->withInput();
        }
    }

    /**
     * Delete a job offer.
     */
    public function destroyOffer(int $id): RedirectResponse
    {
        try {
            $job = JobOffer::findOrFail($id);
            if (Auth::id() !== $job->user_id && Auth::id() !== $job->employer_id) {
                abort(403);
            }

            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }
            if ($job->cover_image) {
                Storage::disk('public')->delete($job->cover_image);
            }

            $job->applications()->delete();
            $job->delete();

            return redirect()->route('user.jobs.my-offers')->with('success', 'Offre supprimée avec succès.');
        } catch (\Throwable $e) {
            \Log::error('Erreur suppression offre: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'offre.');
        }
    }

    public function approveApplication(int $applicationId): RedirectResponse
    {
        try {
            $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
            $recruiter   = Auth::user();

            if ($application->jobOffer?->employer_id !== $recruiter->id && $application->jobOffer?->user_id !== $recruiter->id) {
                abort(403, 'Vous n\'êtes pas autorisé à gérer cette candidature.');
            }

            $application->update(['status' => 'approved']);

            Notification::create([
                'user_id'      => $application->user_id,
                'type'         => 'application_approved',
                'related_type' => 'application',
                'related_id'   => $application->id,
                'title'        => 'Candidature acceptée 🎉',
                'message'      => "Votre candidature pour {$application->jobOffer->title} chez {$application->jobOffer->company_name} a été acceptée !",
                'action_url'   => route('user.applications.index'),
                'is_read'      => false,
            ]);

            // Create / retrieve conversation and send automatic system message
            $conversation = Conversation::findOrCreateBetween($application->user_id, $recruiter->id, 'job', $application->id);

            $systemText = "Votre candidature pour \"" . ($application->jobOffer->title ?? 'ce poste') . "\" a été acceptée. " .
                          "{$recruiter->name} vous contactera prochainement pour planifier un entretien.";

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $recruiter->id,
                'receiver_id'     => $application->user_id,
                'content'         => $systemText,
                'message'         => $systemText,
                'is_read'         => false,
                'is_system'       => true,
            ]);

            return back()->with('success', 'Candidature approuvée et message envoyé au candidat.');
        } catch (\Throwable $e) {
            \Log::error('Erreur approbation candidature: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'approbation de la candidature.');
        }
    }

    public function rejectApplication(Request $request, int $applicationId): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
            $recruiter   = Auth::user();
            $reason      = $request->input('rejection_reason');

            if ($application->jobOffer?->employer_id !== $recruiter->id && $application->jobOffer?->user_id !== $recruiter->id) {
                abort(403, 'Vous n\'êtes pas autorisé à gérer cette candidature.');
            }

            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => $reason,
            ]);

            Notification::create([
                'user_id'      => $application->user_id,
                'type'         => 'application_rejected',
                'related_type' => 'application',
                'related_id'   => $application->id,
                'title'        => 'Candidature non retenue',
                'message'      => "Votre candidature pour {$application->jobOffer->title} chez {$application->jobOffer->company_name} n'a pas été retenue.",
                'action_url'   => route('user.applications.index'),
                'is_read'      => false,
            ]);

            // Create / retrieve conversation and send automatic system message
            $conversation = Conversation::findOrCreateBetween($application->user_id, $recruiter->id, 'job', $application->id);

            $systemText = "Votre candidature pour \"" . ($application->jobOffer->title ?? 'ce poste') . "\" n'a pas été retenue. Motif : " . $reason;

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $recruiter->id,
                'receiver_id'     => $application->user_id,
                'content'         => $systemText,
                'message'         => $systemText,
                'is_read'         => false,
                'is_system'       => true,
            ]);

            return back()->with('info', 'Candidature refusée et message envoyé au candidat.');
        } catch (\Throwable $e) {
            \Log::error('Erreur refus candidature: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du refus de la candidature.');
        }
    }

    public function interviewApplication(int $applicationId): RedirectResponse
    {
        try {
            $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
            $user        = Auth::user();

            if ($application->jobOffer?->employer_id !== $user->id && $application->jobOffer?->user_id !== $user->id) {
                abort(403, 'Vous n\'êtes pas autorisé à gérer cette candidature.');
            }

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
        } catch (\Throwable $e) {
            \Log::error('Erreur entretien candidature: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la programmation de l\'entretien.');
        }
    }

    public function hireApplication(int $applicationId): RedirectResponse
    {
        try {
            $application = JobApplication::with('jobOffer', 'user')->findOrFail($applicationId);
            $recruiter   = Auth::user();

            if ($application->jobOffer?->employer_id !== $recruiter->id && $application->jobOffer?->user_id !== $recruiter->id) {
                abort(403, 'Vous n\'êtes pas autorisé à gérer cette candidature.');
            }

            $application->update(['status' => 'hired']);

            Notification::create([
                'user_id'      => $application->user_id,
                'type'         => 'application_hired',
                'related_type' => 'application',
                'related_id'   => $application->id,
                'title'        => 'Félicitations, vous êtes embauché(e) ! 🎊',
                'message'      => "Vous avez été embauché(e) pour \"{$application->jobOffer->title}\" chez {$application->jobOffer->company_name}.",
                'action_url'   => route('user.applications.index'),
                'is_read'      => false,
            ]);

            $conversation = Conversation::findOrCreateBetween($application->user_id, $recruiter->id, 'job', $application->id);

            $systemText = "Félicitations ! Votre candidature pour \"{$application->jobOffer->title}\" a été retenue et vous êtes officiellement embauché(e). " .
                          "{$recruiter->name} vous contactera prochainement pour les prochaines étapes.";

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $recruiter->id,
                'receiver_id'     => $application->user_id,
                'content'         => $systemText,
                'message'         => $systemText,
                'is_read'         => false,
                'is_system'       => true,
            ]);

            return back()->with('success', 'Candidat marqué comme embauché et notifié.');
        } catch (\Throwable $e) {
            \Log::error('Erreur embauche candidature: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'embauche du candidat.');
        }
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

        // Apply Premium locking mask
        $applications->getCollection()->transform(function (JobApplication $app) {
            if ($app->is_premium_locked) {
                if ($app->relationLoaded('user')) {
                    $maskedUser = clone $app->user;
                    $maskedUser->name = 'Candidat';
                    $maskedUser->email = '***@***.***';
                    $maskedUser->phone = '**********';
                    $maskedUser->profile_photo = null;
                    $app->setRelation('user', $maskedUser);
                }
                if ($app->relationLoaded('cv')) {
                    $app->setRelation('cv', null);
                }
                $app->cv_attachment = null;
            }
            return $app;
        });

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

        // Authorization: ensure application belongs to recruiter's job
        if ($application->jobOffer?->employer_id !== auth()->id() && $application->jobOffer?->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à consulter cette candidature.');
        }

        if ($application->is_premium_locked) {
            abort(403, 'Cette candidature est verrouillée. Passez au plan Premium pour la consulter.');
        }

        return response()->json(['success' => true, 'application' => $application]);
    }

    public function downloadApplicationCv(int $id)
    {
        $application = JobApplication::with('jobOffer')->findOrFail($id);
        $user = Auth::user();

        $isApplicant = $application->user_id === $user->id;
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);
        $isEmployer = $application->jobOffer && (
            $application->jobOffer->employer_id === $user->id ||
            $application->jobOffer->user_id === $user->id
        );

        if (!$isApplicant && !$isAdmin && !$isEmployer) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        $path = $application->cv_attachment;
        if (empty($path) || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'Le fichier CV est introuvable sur le serveur.');
        }

        return Storage::disk('public')->download($path);
    }
}
