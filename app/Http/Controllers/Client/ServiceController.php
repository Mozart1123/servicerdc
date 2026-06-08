<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\SendServiceRequestForm;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * GET /client/services
     * Browse all active published services.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::where('status', 'active')
            ->with('artisan:id,name,email,phone');

        // Search
        if ($term = $request->query('search')) {
            $query->search($term);
        }
        // Filter by category
        if ($cat = $request->query('category_id')) {
            $query->byCategory($cat);
        }
        // Filter by city
        if ($city = $request->query('city')) {
            $query->byLocation($city);
        }
        // Sort
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->orderByDesc('rating'),
            default      => $query->orderByDesc('created_at'),
        };

        $services = $query->paginate((int) $request->query('per_page', 12));

        return response()->json($services);
    }

    /**
     * GET /client/services/{id}
     * View a single service and notify artisan of the view.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $service = Service::with([
            'artisan:id,name,email,phone',
            'category:id,name',
        ])->findOrFail($id);

        // Notify artisan that client viewed their service
        NotificationService::serviceViewed(
            artisanId:    $service->artisan_id,
            clientName:   $request->user()->name,
            serviceTitle: $service->title,
            serviceId:    $service->id,
        );

        return response()->json([
            'data' => $service,
        ]);
    }

    /**
     * POST /client/services/{id}/request
     * Send a service request to an artisan.
     */
    public function storeRequest(SendServiceRequestForm $request, int $id): JsonResponse
    {
        $service = Service::with('artisan')->findOrFail($id);
        $client  = $request->user();

        // Business rule: one pending request per service
        if (ServiceRequest::where('user_id', $client->id)
            ->where('service_id', $id)
            ->where('status', 'pending')
            ->exists()
        ) {
            return response()->json([
                'message' => 'Vous avez déjà une demande en attente pour ce service.',
            ], 409);
        }

        $validated      = $request->validated();
        $serviceRequest = ServiceRequest::create(array_merge($validated, [
            'user_id'    => $client->id,
            'service_id' => $id,
            'status'     => 'pending',
            'email'      => $validated['email'] ?? $client->email,
            'phone'      => $validated['phone'] ?? $client->phone,
        ]));

        // Notify artisan
        NotificationService::serviceRequested(
            artisanId:     $service->artisan_id,
            clientName:    $client->name,
            clientContact: $client->phone ?? $client->email,
            serviceTitle:  $service->title,
            requestId:     $serviceRequest->id,
        );

        return response()->json([
            'message' => 'Votre demande a été envoyée à l\'artisan.',
            'data'    => [
                'request_id' => $serviceRequest->id,
                'status'     => 'pending',
                'service'    => [
                    'id'    => $service->id,
                    'title' => $service->title,
                ],
                'artisan' => [
                    'name'  => $service->artisan?->name,
                    'phone' => $service->artisan?->phone,
                    'email' => $service->artisan?->email,
                ],
            ],
        ], 201);
    }
}
