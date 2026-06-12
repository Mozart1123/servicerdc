<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtisanRating;
use App\Models\Conversation;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\Review;
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
        $serviceRequest->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);
        $artisan = Auth::user();

        // Create a Mission to track the work (start/end dates, rating, feedback)
        Mission::firstOrCreate(
            [
                'service_id'  => $serviceRequest->service_id,
                'client_id'   => $serviceRequest->user_id,
                'artisan_id'  => $artisan->id,
                'title'       => $serviceRequest->requested_service_name ?? ($serviceRequest->service->title ?? 'Mission'),
            ],
            [
                'description' => $serviceRequest->description,
                'status'      => 'pending',
                'amount'      => $serviceRequest->budget_max ?? 0,
                'start_date'  => now(),
            ]
        );

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

        return back()->with('success', 'Demande acceptée. Mission créée, chronomètre lancé !');
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

    public function artisanReviews(): View
    {
        $user = Auth::user();

        $missionReviews = Review::forArtisan($user->id)
            ->with('client', 'mission')
            ->latest()
            ->get();

        $serviceRatings = ArtisanRating::where('artisan_id', $user->id)
            ->with('user', 'serviceRequest')
            ->latest()
            ->get();

        $allReviews = $missionReviews->map(fn($r) => (object)[
            'id'        => 'review_' . $r->id,
            'source'    => 'mission',
            'client'    => $r->client,
            'rating'    => $r->rating,
            'comment'   => $r->feedback,
            'status'    => $r->status,
            'status_label' => $r->status_label,
            'service_name' => $r->mission?->title ?? 'Mission',
            'date'      => $r->created_at,
        ])->merge($serviceRatings->map(fn($r) => (object)[
            'id'        => 'rating_' . $r->id,
            'source'    => 'service',
            'client'    => $r->user,
            'rating'    => $r->rating,
            'comment'   => $r->comment,
            'status'    => 'approved',
            'status_label' => 'Approuvé',
            'service_name' => $r->serviceRequest?->requested_service_name ?? 'Service',
            'date'      => $r->created_at,
        ]))->sortByDesc('date')->values();

        $avgRating = $allReviews->avg('rating') ?? 0;
        $totalReviews = $allReviews->count();
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = $allReviews->where('rating', $i)->count();
        }

        return view('user.artisan.reviews', compact(
            'allReviews', 'avgRating', 'totalReviews', 'ratingDistribution'
        ));
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

    /**
     * Artisan starts a mission (marks in_progress).
     */
    public function startMission(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update(['status' => 'accepted']);

        $mission = Mission::where('artisan_id', Auth::id())
            ->where(function ($q) use ($serviceRequest) {
                $q->where('service_id', $serviceRequest->service_id)
                  ->orWhere('title', $serviceRequest->requested_service_name);
            })
            ->where('status', 'pending')
            ->first();

        if ($mission) {
            $mission->update([
                'status'     => 'in_progress',
                'start_date' => now(),
            ]);
        }

        Notification::create([
            'user_id'    => $serviceRequest->user_id,
            'type'       => 'mission_started',
            'title'      => 'Mission démarrée !',
            'message'    => "L'artisan a commencé le travail sur votre demande.",
            'action_url' => route('user.missions.index'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'Mission démarrée avec succès !');
    }

    /**
     * Artisan marks the work as complete.
     */
    public function complete(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $mission = Mission::where('artisan_id', Auth::id())
            ->where(function ($q) use ($serviceRequest) {
                $q->where('service_id', $serviceRequest->service_id)
                  ->orWhere('title', $serviceRequest->requested_service_name);
            })
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($mission) {
            $mission->update([
                'status'   => 'completed',
                'end_date' => now(),
            ]);
        }

        Notification::create([
            'user_id'    => $serviceRequest->user_id,
            'type'       => 'mission_completed',
            'title'      => 'Mission terminée ✅',
            'message'    => "L'artisan a terminé le travail. N'oubliez pas de laisser un avis !",
            'action_url' => route('user.missions.index'),
            'is_read'    => false,
        ]);

        // Notify all admins about the completed mission
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => 'admin_mission_completed',
                'title'      => 'Mission terminée',
                'message'    => "La mission #{$serviceRequest->id} a été complétée. En attente de l'avis client.",
                'action_url' => route('admin.missions.index'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Mission marquée comme terminée ! Le client peut maintenant laisser un avis.');
    }

    /**
     * Cancel a service request.
     */
    public function cancel(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update(['status' => 'cancelled']);

        $mission = Mission::where('artisan_id', Auth::id())
            ->where(function ($q) use ($serviceRequest) {
                $q->where('service_id', $serviceRequest->service_id)
                  ->orWhere('title', $serviceRequest->requested_service_name);
            })
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->first();

        if ($mission) {
            $mission->update([
                'status'   => 'cancelled',
                'end_date' => now(),
            ]);
        }

        Notification::create([
            'user_id'    => $serviceRequest->user_id,
            'type'       => 'mission_cancelled',
            'title'      => 'Mission annulée',
            'message'    => "La mission a été annulée.",
            'action_url' => route('user.service-requests.index'),
            'is_read'    => false,
        ]);

        return back()->with('info', 'Mission annulée.');
    }

    /**
     * Rate an artisan after service completion.
     */
    public function rate(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $user = Auth::user();

        if ($serviceRequest->user_id !== $user->id) {
            abort(403);
        }
        if ($serviceRequest->status !== 'completed') {
            return back()->with('error', 'Vous ne pouvez evaluer qu\'un service termine.');
        }
        if ($serviceRequest->rating) {
            return back()->with('error', 'Vous avez deja evaluet cet artisan.');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if (!$artisanId) {
            return back()->with('error', 'Artisan introuvable.');
        }

        ArtisanRating::create([
            'user_id'            => $user->id,
            'artisan_id'         => $artisanId,
            'service_request_id' => $serviceRequest->id,
            'rating'             => $validated['rating'],
            'comment'            => $validated['comment'] ?? null,
        ]);

        Notification::create([
            'user_id'    => $artisanId,
            'type'       => 'artisan_rated',
            'title'      => 'Nouvelle evaluation',
            'message'    => "{$user->name} vous a donne {$validated['rating']}/5 etoiles.",
            'action_url' => route('user.artisan.service-requests.index'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'Merci pour votre evaluation !');
    }
}