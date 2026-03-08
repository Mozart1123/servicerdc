<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Service Request Controller
 * 
 * Handles custom service requests from users who don't find what they're looking for.
 */
class ServiceRequestController extends Controller
{
    /**
     * Store a new service request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requested_service_name' => ['required', 'string', 'max:255'],
            'category_needed' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'urgency' => ['nullable', 'in:low,medium,high,urgent'],
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['email'] = $validated['email'] ?? $user->email;
        $validated['phone'] = $validated['phone'] ?? $user->phone;
        $validated['status'] = 'pending';
        $validated['urgency'] = $validated['urgency'] ?? 'medium';

        $serviceRequest = ServiceRequest::create($validated);

        // Notify admins of new custom service request
        $adminUsers = \App\Models\User::where('role', 'admin')
                                     ->orWhere('role', 'super_admin')
                                     ->get();

        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'custom_service_request',
                'title' => 'Nouvelle demande de service personnalisée',
                'message' => "{$user->name} demande : {$serviceRequest->requested_service_name}",
                'data' => [
                    'service_request_id' => $serviceRequest->id,
                    'user_id' => $user->id,
                    'urgency' => $serviceRequest->urgency,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Merci ! Votre demande a été envoyée à nos administrateurs. Nous vous répondrons dans les plus brefs délais.',
        ]);
    }

    /**
     * Display all custom service requests for the current user.
     */
    public function index(): View
    {
        $user = Auth::user();
        $serviceRequests = $user->serviceRequests()
                              ->latest()
                              ->paginate(10);

        $stats = [
            'pending' => $user->serviceRequests()->pending()->count(),
            'addressed' => $user->serviceRequests()->addressed()->count(),
        ];

        return view('user.service-requests.index', compact('serviceRequests', 'stats'));
    }

    /**
     * Display a specific service request with admin response.
     */
    public function show(ServiceRequest $serviceRequest): View
    {
        // Authorize
        if (Auth::id() !== $serviceRequest->user_id) {
            abort(403, 'Non autorisé');
        }

        return view('user.service-requests.show', compact('serviceRequest'));
    }

    /**
     * Display all service requests for admin (MOSALA+ Integration)
     */
    public function adminIndex(): View
    {
        $serviceRequests = ServiceRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'pending' => ServiceRequest::pending()->count(),
            'addressed' => ServiceRequest::addressed()->count(),
            'total' => ServiceRequest::count(),
        ];

        return view('admin.service-requests.index', compact('serviceRequests', 'stats'));
    }

    /**
     * Admin responds to a service request (MOSALA+ Integration)
     */
    public function adminRespond(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'response' => 'required|string|max:1000',
            'status' => 'required|in:pending,addressed,resolved',
        ]);

        $serviceRequest->update([
            'status' => $validated['status'],
            'admin_response' => $validated['response'],
            'responded_at' => now(),
            'responded_by' => Auth::id(),
        ]);

        // Notify user of admin response
        Notification::create([
            'user_id' => $serviceRequest->user_id,
            'type' => 'service_request_response',
            'title' => 'Réponse à votre demande de service',
            'message' => 'Un administrateur a répondu à votre demande: ' . $serviceRequest->requested_service_name,
            'data' => [
                'service_request_id' => $serviceRequest->id,
                'admin_id' => Auth::id(),
            ],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Réponse envoyée avec succès.',
                'status' => $serviceRequest->status,
            ]);
        }

        return back()->with('success', 'Réponse envoyée avec succès à l\'utilisateur.');
    }
}