<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    /**
     * GET /client/jobs
     * Browse all active job offers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobOffer::where('status', 'active')
            ->notExpired()
            ->with('employer:id,name,email');

        if ($search = $request->query('search')) {
            $query->search($search);
        }
        if ($type = $request->query('contract_type')) {
            $query->byContractType($type);
        }
        if ($location = $request->query('location')) {
            $query->byLocation($location);
        }
        if ($category = $request->query('category')) {
            $query->byCategory($category);
        }

        $query->orderByDesc('is_urgent')
              ->orderByRaw("(SELECT COUNT(*) FROM subscriptions 
                             INNER JOIN subscription_plans ON subscriptions.subscription_plan_id = subscription_plans.id
                             WHERE subscriptions.user_id = job_offers.employer_id 
                             AND subscriptions.status = 'active'
                             AND (subscriptions.ends_at IS NULL OR subscriptions.ends_at >= NOW())
                             AND subscription_plans.slug = 'recruiter-premium') DESC")
              ->orderByDesc('created_at');

        $jobs = $query->paginate((int) $request->query('per_page', 10));

        // Attach has_applied flag for the authenticated user
        $userId = $request->user()->id;
        $appliedIds = JobApplication::where('user_id', $userId)
            ->pluck('job_offer_id')
            ->toArray();

        $jobs->getCollection()->transform(function (JobOffer $job) use ($appliedIds) {
            $job->has_applied = in_array($job->id, $appliedIds);
            return $job;
        });

        return response()->json($jobs);
    }

    /**
     * GET /client/jobs/{id}
     * View full details of a job offer.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $job = JobOffer::with([
            'employer:id,name,email',
        ])->findOrFail($id);

        $hasApplied = JobApplication::where('user_id', $request->user()->id)
            ->where('job_offer_id', $id)
            ->exists();

        return response()->json([
            'data'        => $job,
            'has_applied' => $hasApplied,
            'has_cv'      => (bool) $request->user()->cv,
        ]);
    }

    /**
     * POST /client/jobs/{id}/apply
     * Apply for a job — requires CV.
     */
    public function apply(Request $request, int $id): JsonResponse
    {
        $client = $request->user();

        // Business rule: must have a CV
        if (!$client->cv) {
            return response()->json([
                'message'        => 'Vous devez créer un CV avant de postuler à une offre d\'emploi.',
                'redirect_to_cv' => true,
            ], 403);
        }

        $job = JobOffer::findOrFail($id);

        // Business rule: job must be open
        if ($job->status !== 'active') {
            return response()->json([
                'message' => 'Cette offre d\'emploi est fermée.',
            ], 422);
        }

        // Business rule: no duplicate applications
        if (JobApplication::where('user_id', $client->id)
            ->where('job_offer_id', $id)
            ->exists()
        ) {
            return response()->json([
                'message' => 'Vous avez déjà postulé à cette offre d\'emploi.',
            ], 409);
        }

        $application = JobApplication::create([
            'user_id'      => $client->id,
            'job_offer_id' => $id,
            'status'       => 'pending',
            'applied_at'   => now(),
        ]);

        // Notify recruiter
        NotificationService::jobApplicationReceived(
            recruiterId:   $job->employer_id,
            applicantName: $client->name,
            jobTitle:      $job->title,
            applicationId: $application->id,
        );

        return response()->json([
            'message' => 'Votre candidature a été soumise avec succès.',
            'data'    => [
                'application_id' => $application->id,
                'status'         => 'pending',
                'job'            => [
                    'id'    => $job->id,
                    'title' => $job->title,
                ],
                'applied_at' => $application->created_at->format('d/m/Y H:i'),
            ],
        ], 201);
    }

    /**
     * GET /client/cv (redirect helper via jobs flow)
     * Check if user has CV — used by frontend before showing Apply button.
     */
    public function checkCv(Request $request): JsonResponse
    {
        $cv = $request->user()->cv;

        return response()->json([
            'has_cv' => (bool) $cv,
            'cv'     => $cv ? [
                'full_name'    => $cv->full_name,
                'email'        => $cv->email,
                'phone_number' => $cv->phone_number,
            ] : null,
        ]);
    }
}
