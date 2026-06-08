<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruiter\StoreJobOfferRequest;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    /**
     * GET /recruiter/jobs
     * List all job offers by this recruiter.
     */
    public function index(Request $request): JsonResponse
    {
        $jobs = JobOffer::where('employer_id', $request->user()->id)
            ->withCount(['applications', 'applications as pending_count' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data'  => $jobs,
            'total' => $jobs->count(),
        ]);
    }

    /**
     * POST /recruiter/jobs
     * Publish a new job offer — automatically visible to clients.
     */
    public function store(StoreJobOfferRequest $request): JsonResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        $job = JobOffer::create(array_merge($validated, [
            'employer_id'  => $user->id,
            'company_name' => $user->name,
            'status'       => $validated['status'] ?? 'active',
        ]));

        return response()->json([
            'message' => 'Offre d\'emploi publiée avec succès. Elle est désormais visible par les candidats.',
            'data'    => $job->fresh(),
        ], 201);
    }

    /**
     * GET /recruiter/applications
     * All applications received for recruiter's job offers.
     */
    public function applications(Request $request): JsonResponse
    {
        $query = JobApplication::whereHas('jobOffer', function ($q) use ($request) {
            $q->where('employer_id', $request->user()->id);
        })->with([
            'user:id,name,email,phone',
            'user.cv',
            'jobOffer:id,title,location,contract_type,deadline',
        ])->orderByDesc('created_at');

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        // Filter by job
        if ($jobId = $request->query('job_offer_id')) {
            $query->where('job_offer_id', $jobId);
        }

        $applications = $query->get()->map(fn(JobApplication $app) => [
            'id'           => $app->id,
            'status'       => $app->status,
            'status_label' => $app->status_label,
            'applied_at'   => $app->created_at?->format('d/m/Y H:i'),
            'job'          => [
                'id'            => $app->jobOffer?->id,
                'title'         => $app->jobOffer?->title,
                'location'      => $app->jobOffer?->location,
                'contract_type' => $app->jobOffer?->contract_type,
                'deadline'      => optional($app->jobOffer?->deadline)->format('d/m/Y') ?? (string)($app->jobOffer?->deadline ?? ''),
            ],
            'applicant' => [
                'id'    => $app->user?->id,
                'name'  => $app->user?->name,
                'email' => $app->user?->email,
                'phone' => $app->user?->phone,
            ],
            'cv' => $app->user?->cv ? [
                'full_name'      => $app->user->cv->full_name,
                'email'          => $app->user->cv->email,
                'phone_number'   => $app->user->cv->phone_number,
                'address'        => $app->user->cv->address,
                'education'      => $app->user->cv->education,
                'skills'         => $app->user->cv->skills,
                'experience'     => $app->user->cv->experience,
                'languages'      => $app->user->cv->languages,
                'cv_file_path'   => $app->user->cv->cv_file_path,
                'portfolio_link' => $app->user->cv->portfolio_link,
            ] : null,
        ]);

        return response()->json([
            'data'     => $applications,
            'total'    => $applications->count(),
            'pending'  => $applications->where('status', 'pending')->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ]);
    }

    /**
     * GET /recruiter/applications/{id}
     * View a single application with full CV details.
     */
    public function showApplication(int $id): JsonResponse
    {
        $app = JobApplication::with([
            'user:id,name,email,phone',
            'user.cv',
            'jobOffer',
        ])->findOrFail($id);

        // Authorization: ensure application belongs to recruiter's job
        if ($app->jobOffer?->employer_id !== request()->user()->id) {
            abort(403, 'Vous n\'êtes pas autorisé à consulter cette candidature.');
        }

        return response()->json(['data' => $app]);
    }

    /**
     * POST /recruiter/applications/{id}/approve
     */
    public function approveApplication(int $id): JsonResponse
    {
        $app = $this->findAndAuthorizeApplication($id);

        $app->update(['status' => 'approved', 'reviewed_at' => now()]);

        NotificationService::jobApplicationApproved(
            clientId:      $app->user_id,
            jobTitle:      $app->jobOffer->title,
            applicationId: $app->id,
        );

        return response()->json([
            'message' => 'Candidature approuvée. Le candidat a été notifié.',
            'data'    => $app->fresh(),
        ]);
    }

    /**
     * POST /recruiter/applications/{id}/reject
     */
    public function rejectApplication(int $id): JsonResponse
    {
        $app = $this->findAndAuthorizeApplication($id);

        $app->update(['status' => 'rejected', 'reviewed_at' => now()]);

        NotificationService::jobApplicationRejected(
            clientId:      $app->user_id,
            jobTitle:      $app->jobOffer->title,
            applicationId: $app->id,
        );

        return response()->json([
            'message' => 'Candidature rejetée. Le candidat a été notifié.',
            'data'    => $app->fresh(),
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function findAndAuthorizeApplication(int $id): JobApplication
    {
        $app = JobApplication::with('jobOffer')->findOrFail($id);

        if ($app->jobOffer?->employer_id !== request()->user()->id) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer cette candidature.');
        }

        return $app;
    }
}
