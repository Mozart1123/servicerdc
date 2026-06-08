<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artisan\StoreServiceRequest;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * GET /artisan/services
     * List all services published by the authenticated artisan.
     */
    public function index(Request $request): JsonResponse
    {
        $services = Service::where('artisan_id', $request->user()->id)
            ->with('category')
            ->withCount('serviceRequests')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data'  => $services,
            'total' => $services->count(),
        ]);
    }

    /**
     * POST /artisan/services
     * Publish a new service — automatically visible to clients.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        $service = Service::create(array_merge($validated, [
            'artisan_id'    => $user->id,
            'provider_name' => $user->name,
            'profession'    => $user->profession ?? 'Artisan',
            'city'          => explode(',', $validated['location'])[0] ?? $validated['location'],
            'phone_number'  => $user->phone ?? '',
            'status'        => $validated['status'] ?? 'active',
        ]));

        return response()->json([
            'message' => 'Service publié avec succès. Il est désormais visible par les clients.',
            'data'    => $service->load('category'),
        ], 201);
    }

    /**
     * GET /artisan/service-requests
     * All service requests directed to this artisan's services.
     */
    public function requests(Request $request): JsonResponse
    {
        $requests = ServiceRequest::whereHas('service', function ($q) use ($request) {
            $q->where('artisan_id', $request->user()->id);
        })
        ->with(['user:id,name,email,phone', 'service:id,title,price,location'])
        ->orderByDesc('created_at')
        ->get()
        ->map(function (ServiceRequest $sr) {
            return [
                'id'             => $sr->id,
                'status'         => $sr->status,
                'status_label'   => $sr->status_label,
                'description'    => $sr->description,
                'urgency'        => $sr->urgency,
                'budget_range'   => $sr->budget_range,
                'created_at'     => $sr->created_at->format('d/m/Y H:i'),
                'client' => [
                    'id'    => $sr->user->id,
                    'name'  => $sr->user->name,
                    'email' => $sr->user->email,
                    'phone' => $sr->user->phone ?? $sr->phone,
                ],
                'service' => [
                    'id'       => $sr->service?->id,
                    'title'    => $sr->service?->title,
                    'price'    => $sr->service?->price,
                    'location' => $sr->service?->location,
                ],
            ];
        });

        return response()->json([
            'data'    => $requests,
            'total'   => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
        ]);
    }

    /**
     * POST /artisan/service-requests/{id}/accept
     */
    public function acceptRequest(int $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::with(['user', 'service'])->findOrFail($id);

        // Authorization: ensure this request is for the current artisan's service
        $this->authorizeServiceRequest($serviceRequest);

        $serviceRequest->update([
            'status'       => 'accepted',
            'responded_at' => now(),
        ]);

        NotificationService::serviceRequestAccepted(
            clientId:     $serviceRequest->user_id,
            serviceTitle: $serviceRequest->service->title,
            requestId:    $serviceRequest->id,
        );

        return response()->json([
            'message' => 'Demande acceptée. Le client a été notifié.',
            'data'    => $serviceRequest->fresh(['user', 'service']),
        ]);
    }

    /**
     * POST /artisan/service-requests/{id}/reject
     */
    public function rejectRequest(int $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::with(['user', 'service'])->findOrFail($id);

        $this->authorizeServiceRequest($serviceRequest);

        $serviceRequest->update([
            'status'       => 'rejected',
            'responded_at' => now(),
        ]);

        NotificationService::serviceRequestRejected(
            clientId:     $serviceRequest->user_id,
            serviceTitle: $serviceRequest->service->title,
            requestId:    $serviceRequest->id,
        );

        return response()->json([
            'message' => 'Demande rejetée. Le client a été notifié.',
            'data'    => $serviceRequest->fresh(['user', 'service']),
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function authorizeServiceRequest(ServiceRequest $sr): void
    {
        $artisanId = request()->user()->id;

        if ($sr->service?->artisan_id !== $artisanId) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer cette demande.');
        }
    }
}
