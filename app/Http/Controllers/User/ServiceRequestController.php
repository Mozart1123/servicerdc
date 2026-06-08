<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    /**
     * Store a new service request (client sends).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'service_id'             => ['nullable', 'exists:services,id'],
            'requested_service_name' => ['nullable', 'string', 'max:255'],
            'description'            => ['nullable', 'string', 'max:1000'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'phone'                  => ['nullable', 'string', 'max:20'],
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['status']  = 'pending';

        $artisanId = null;
        if (!empty($validated['service_id'])) {
            $service   = Service::find($validated['service_id']);
            $artisanId = $service?->artisan_id;
            $validated['artisan_id'] = $artisanId;
        }

        $serviceRequest = ServiceRequest::create($validated);

        if ($artisanId) {
            Notification::create([
                'user_id'      => $artisanId,
                'type'         => 'service_request',
                'related_type' => 'request',
                'related_id'   => $serviceRequest->id,
                'title'        => 'Nouvelle demande',
                'message'      => "{$user->name} vous sollicite pour : " . ($serviceRequest->requested_service_name ?? 'un service'),
                'action_url'   => route('user.artisan.service-requests.index'),
                'is_read'      => false,
            ]);
        }

        return back()->with('success', 'Votre demande a été envoyée.');
    }

    public function accept(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update(['status' => 'accepted']);
        $artisan = Auth::user();

        Notification::create([
            'user_id'      => $serviceRequest->user_id,
            'type'         => 'service_accepted',
            'related_type' => 'request',
            'related_id'   => $serviceRequest->id,
            'title'        => 'Demande acceptée 🎉',
            'message'      => "L'artisan {$artisan->name} a accepté votre demande.",
            'action_url'   => route('user.service-requests.index'),
            'is_read'      => false,
        ]);

        Conversation::findOrCreateBetween($serviceRequest->user_id, $artisan->id, 'service', $serviceRequest->id);

        return back()->with('success', 'Demande acceptée.');
    }

    public function reject(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update(['status' => 'rejected']);
        $artisan = Auth::user();

        Notification::create([
            'user_id'      => $serviceRequest->user_id,
            'type'         => 'service_rejected',
            'related_type' => 'request',
            'related_id'   => $serviceRequest->id,
            'title'        => 'Demande refusée',
            'message'      => "L'artisan {$artisan->name} n'est pas disponible.",
            'action_url'   => route('user.service-requests.index'),
            'is_read'      => false,
        ]);

        return back()->with('info', 'Demande refusée.');
    }

    public function artisanRequests(): View
    {
        $user = Auth::user();
        
        $query = ServiceRequest::where('artisan_id', $user->id)
            ->with('user', 'service')
            ->latest();

        $stats = [
            'total'     => ServiceRequest::where('artisan_id', $user->id)->count(),
            'pending'   => ServiceRequest::where('artisan_id', $user->id)->where('status', 'pending')->count(),
            'accepted'  => ServiceRequest::where('artisan_id', $user->id)->where('status', 'accepted')->count(),
            'rejected'  => ServiceRequest::where('artisan_id', $user->id)->where('status', 'rejected')->count(),
            'completed' => ServiceRequest::where('artisan_id', $user->id)->where('status', 'completed')->count(),
        ];

        $serviceRequests = $query->paginate(15);

        return view('user.artisan.service-requests', compact('serviceRequests', 'stats'));
    }

    public function index(): View
    {
        $user = Auth::user();
        
        $query = $user->serviceRequests()
            ->with('service.artisan')
            ->latest();

        $stats = [
            'total'     => $user->serviceRequests()->count(),
            'pending'   => $user->serviceRequests()->where('status', 'pending')->count(),
            'accepted'  => $user->serviceRequests()->where('status', 'accepted')->count(),
            'rejected'  => $user->serviceRequests()->where('status', 'rejected')->count(),
            'completed' => $user->serviceRequests()->where('status', 'completed')->count(),
        ];

        $serviceRequests = $query->paginate(15);

        return view('user.service-requests.index', compact('serviceRequests', 'stats'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $serviceRequest->load('user', 'service.artisan');
        return view('user.service-requests.show', compact('serviceRequest'));
    }
}