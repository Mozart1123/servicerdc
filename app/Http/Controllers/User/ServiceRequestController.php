<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Conversation;
use App\Models\Mission;
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

            // Create/open conversation
            $conversation = \App\Models\Conversation::findOrCreateBetween($user->id, $artisanId);
            
            // Send the automated first message from the client
            $automatedMessage = "Bonjour,\nJe souhaiterais faire appel à vos services pour : " . ($serviceRequest->requested_service_name ?? 'un service') . ".\n\n" .
                                "📍 Lieu : " . ($serviceRequest->city ?? 'Non précisé') . "\n" .
                                "💰 Budget estimé : " . ($request->budget_range ?? 'Non précisé') . "\n" .
                                "⏳ Urgence : " . ($request->urgency ?? 'Standard') . "\n\n" .
                                "📝 Description :\n" . $serviceRequest->description;

            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'content'         => $automatedMessage,
                'message'         => $automatedMessage,
                'is_read'         => false,
            ]);

            return redirect()->route('user.messages.index', ['id' => $conversation->id])
                             ->with('success', 'Votre demande a été envoyée. Vous pouvez maintenant échanger avec l\'artisan.');
        }

        return redirect()->route('user.service-requests.index')->with('success', 'Votre demande a été enregistrée.');
    }

    public function accept(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update([
            'status'      => 'accepted',
            // accepted_at is set when payment is made (chrono starts)
        ]);
        $artisan = Auth::user();

        // Create a Mission to track the work (start/end dates, rating, feedback)
        Mission::firstOrCreate(
            [
                'service_request_id' => $serviceRequest->id,
                'service_id'  => $serviceRequest->service_id,
                'client_id'   => $serviceRequest->user_id,
                'artisan_id'  => $artisan->id,
            ],
            [
                'title'       => $serviceRequest->requested_service_name ?? ($serviceRequest->service->title ?? 'Mission'),
                'description' => $serviceRequest->description,
                'status'      => 'pending',
                'amount'      => $serviceRequest->budget_max ?? 0,
            ]
        );

        Notification::create([
            'user_id'      => $serviceRequest->user_id,
            'type'         => 'service_accepted',
            'related_type' => 'request',
            'related_id'   => $serviceRequest->id,
            'title'        => 'Demande acceptée 🎉',
            'message'      => "L'artisan {$artisan->name} a accepté votre demande. Choisissez votre mode de paiement pour démarrer.",
            'action_url'   => route('user.service-requests.index'),
            'is_read'      => false,
        ]);

        Conversation::findOrCreateBetween($serviceRequest->user_id, $artisan->id, 'service', $serviceRequest->id);

        return back()->with('success', 'Demande acceptée. En attente du paiement du client pour démarrer le chrono.');
    }

    public function payCash(ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->status !== 'accepted') {
            return back()->with('error', 'Le paiement ne peut être effectué qu\'une fois la demande acceptée.');
        }

        $serviceRequest->update([
            'status'      => 'in_progress',
            'accepted_at' => now(), // Start chrono
        ]);

        if ($serviceRequest->mission) {
            $serviceRequest->mission->update([
                'status'          => 'in_progress',
                'payment_channel' => 'cash',
                'start_date'      => now(),
            ]);
        }

        Notification::create([
            'user_id'    => $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id,
            'type'       => 'mission_started',
            'title'      => 'Paiement en espèces - Mission démarrée',
            'message'    => "Le client a choisi le paiement en espèces. Le chrono a démarré.",
            'action_url'   => route('user.service-requests.index'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'Paiement en espèces sélectionné. Le chrono a démarré !');
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

        $serviceRatings = Review::where('artisan_id', $user->id)
            ->whereNotNull('migrated_from_artisan_rating_id') // from old ArtisanRating
            ->with('client')
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
            'client'    => $r->client,
            'rating'    => $r->rating,
            'comment'   => $r->feedback,
            'status'    => 'approved',
            'status_label' => 'Approuvé',
            'service_name' => 'Service (migré)',
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
        $serviceRequest->load(['user', 'service.artisan', 'mission']);

        // Load conversation if one exists between client and artisan
        $conversation = null;
        $artisanId    = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId) {
            $oneCol = \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
            $twoCol = \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';
            $conversation = \App\Models\Conversation::where(function ($q) use ($serviceRequest, $artisanId, $oneCol, $twoCol) {
                $q->where($oneCol, $serviceRequest->user_id)->where($twoCol, $artisanId);
            })->orWhere(function ($q) use ($serviceRequest, $artisanId, $oneCol, $twoCol) {
                $q->where($oneCol, $artisanId)->where($twoCol, $serviceRequest->user_id);
            })->first();
        }

        return view('user.service-requests.show', compact('serviceRequest', 'conversation'));
    }

    /**
     * Artisan starts a mission (marks in_progress).
     */
    public function startMission(ServiceRequest $serviceRequest): RedirectResponse
    {
        // Guard: only proceed if payment was made (status must be accepted)
        if ($serviceRequest->status !== 'accepted') {
            return back()->with('error', 'La mission ne peut démarrer qu\'après le paiement du client.');
        }

        $serviceRequest->update([
            'status'      => 'in_progress',
            'accepted_at' => now(),
        ]);

        $mission = $serviceRequest->mission;
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
            'action_url' => route('user.service-requests.index'),
            'is_read'    => false,
        ]);

        return back()->with('success', 'Mission démarrée avec succès !');
    }

    /**
     * Artisan marks the work as complete.
     */
    public function complete(ServiceRequest $serviceRequest): RedirectResponse
    {
        // Guard: only artisan can complete
        if ($serviceRequest->artisan_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'in_progress') {
            return back()->with('error', 'Le service doit être en cours pour être terminé.');
        }

        $serviceRequest->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $mission = $serviceRequest->mission;
        if ($mission) {
            $mission->update([
                'status'   => 'completed',
                'end_date' => now(),
                'payout_status' => $mission->payment_channel === 'cash' ? 'pending_payout' : $mission->payout_status,
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

        $mission = $serviceRequest->mission;

        if ($mission && !in_array($mission->status, ['completed', 'cancelled'])) {
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

        Review::create([
            'service_request_id' => $serviceRequest->id,
            'client_id'  => $user->id,
            'artisan_id' => $artisanId,
            'rating'     => $validated['rating'],
            'feedback'   => $validated['comment'] ?? null,
            'status'     => 'approved', // Direct approval for service-request ratings
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