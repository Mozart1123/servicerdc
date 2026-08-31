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

        try {
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
        } catch (\Throwable $e) {
            \Log::error('Erreur création demande service: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi de la demande.')->withInput();
        }
    }

    public function accept(ServiceRequest $serviceRequest): RedirectResponse
    {
        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId !== Auth::id()) {
            abort(403, 'Cette demande ne vous a pas été adressée.');
        }

        try {
            $serviceRequest->update([
                'status'      => 'accepted',
            ]);
            $artisan = Auth::user();

            // Create a Mission to track the work
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
        } catch (\Throwable $e) {
            \Log::error('Erreur acceptation demande service: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'acceptation de la demande.');
        }
    }

    public function payCash(ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->user_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'accepted') {
            return back()->with('error', 'Le paiement ne peut être effectué qu\'une fois la demande acceptée.');
        }

        try {
            $serviceRequest->update([
                'status'      => 'in_progress',
                'accepted_at' => now(), // Start chrono
            ]);

            $serviceRequest->workSessions()->create(['started_at' => now()]);

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
        } catch (\Throwable $e) {
            \Log::error('Erreur paiement espèces: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la validation du paiement.');
        }
    }

    public function reject(ServiceRequest $serviceRequest): RedirectResponse
    {
        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId !== Auth::id()) {
            abort(403, 'Cette demande ne vous a pas été adressée.');
        }

        try {
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
        } catch (\Throwable $e) {
            \Log::error('Erreur refus demande service: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du refus de la demande.');
        }
    }

    public function artisanRequests(): View
    {
        $user = Auth::user();
        
        $query = ServiceRequest::where('artisan_id', $user->id)
            ->with('user', 'service', 'workSessions')
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
            'total'               => $user->serviceRequests()->count(),
            'pending'             => $user->serviceRequests()->where('status', 'pending')->count(),
            'accepted'            => $user->serviceRequests()->where('status', 'accepted')->count(),
            'awaiting_validation' => $user->serviceRequests()->where('status', 'awaiting_validation')->count(),
            'rejected'            => $user->serviceRequests()->where('status', 'rejected')->count(),
            'completed'           => $user->serviceRequests()->where('status', 'completed')->count(),
        ];

        $serviceRequests = $query->paginate(15);

        return view('user.service-requests.index', compact('serviceRequests', 'stats'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        $isClient  = $serviceRequest->user_id === Auth::id();
        $isArtisan = $artisanId === Auth::id();
        $isAdmin   = in_array(Auth::user()->role, ['admin', 'super_admin'], true);

        if (!$isClient && !$isArtisan && !$isAdmin) {
            abort(403, 'Vous n\'êtes pas autorisé à consulter cette demande.');
        }

        $serviceRequest->load(['user', 'service.artisan', 'mission', 'workSessions']);

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

        $amountToPay = 0;
        if ($serviceRequest->mission && (float) $serviceRequest->mission->amount > 0) {
            $amountToPay = (float) $serviceRequest->mission->amount;
        } elseif ($serviceRequest->service && (float) $serviceRequest->service->price > 0) {
            $amountToPay = (float) $serviceRequest->service->price;
        } elseif ((float) $serviceRequest->budget_max > 0) {
            $amountToPay = (float) $serviceRequest->budget_max;
        } else {
            $amountToPay = 10.0;
        }

        return view('user.service-requests.show', compact('serviceRequest', 'conversation', 'amountToPay'));
    }

    /**
     * Artisan starts a mission (marks in_progress).
     */
    public function startMission(ServiceRequest $serviceRequest): RedirectResponse
    {
        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId !== Auth::id()) {
            abort(403);
        }
        // Guard: only proceed if payment was made (status must be accepted)
        if ($serviceRequest->status !== 'accepted') {
            return back()->with('error', 'La mission ne peut démarrer qu\'après le paiement du client.');
        }

        $serviceRequest->update([
            'status'      => 'in_progress',
            'accepted_at' => now(),
        ]);

        $serviceRequest->workSessions()->create(['started_at' => now()]);

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
     * Artisan pauses the work-time clock. Closes the currently active
     * session; the worked total up to now is preserved (it's already summed
     * from the closed session), it just stops growing until resumeWork().
     */
    public function pauseWork(ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->artisan_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'in_progress') {
            return back()->with('error', 'Le service doit être en cours pour être mis en pause.');
        }

        $activeSession = $serviceRequest->activeWorkSession;
        if (!$activeSession) {
            return back()->with('error', 'Le travail est déjà en pause.');
        }

        $activeSession->update(['ended_at' => now()]);

        return back()->with('success', 'Travail mis en pause.');
    }

    /**
     * Artisan resumes the work-time clock. Opens a brand new session — the
     * previous (closed) sessions are left untouched, so nothing is reset and
     * nothing is double-counted.
     */
    public function resumeWork(ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->artisan_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'in_progress') {
            return back()->with('error', 'Le service doit être en cours pour être repris.');
        }
        if ($serviceRequest->activeWorkSession) {
            return back()->with('error', 'Le travail est déjà en cours.');
        }

        $serviceRequest->workSessions()->create(['started_at' => now()]);

        return back()->with('success', 'Travail repris.');
    }

    /**
     * Artisan SIGNALS that the work is done — this does NOT close the mission
     * by itself. It puts the request in 'awaiting_validation' and waits for
     * the client to confirm via validateCompletion() below. This is what
     * gates the mission/service_request 'completed' status (and therefore the
     * rating form, and the payout_status bump for cash missions) behind the
     * CLIENT's confirmation instead of the artisan's own declaration — the
     * artisan can no longer unilaterally close out (and get paid for) a
     * mission the client hasn't actually confirmed as satisfactorily done.
     */
    public function complete(ServiceRequest $serviceRequest): RedirectResponse
    {
        // Guard: only artisan can signal completion
        if ($serviceRequest->artisan_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'in_progress') {
            return back()->with('error', 'Le service doit être en cours pour être marqué comme terminé.');
        }

        $serviceRequest->update([
            'status' => 'awaiting_validation',
        ]);

        // Stop the clock: close whatever work session is still open, even if
        // the artisan forgot to pause first — "signaling done" always ends
        // active work.
        $activeSession = $serviceRequest->activeWorkSession;
        if ($activeSession) {
            $activeSession->update(['ended_at' => now()]);
        }

        // NOTE: mission.status intentionally stays 'in_progress' here — it
        // only becomes 'completed' once the client validates below. The
        // missions.status column is a strict DB enum without an
        // 'awaiting_validation' value, so the finer-grained intermediate
        // state lives on service_requests.status (a plain string column)
        // instead; the mission simply doesn't move until the client acts.

        Notification::create([
            'user_id'    => $serviceRequest->user_id,
            'type'       => 'mission_awaiting_validation',
            'title'      => 'Confirmation requise',
            'message'    => "L'artisan indique que le travail est terminé. Merci de confirmer que la prestation vous convient.",
            'action_url' => route('user.service-requests.show', $serviceRequest->id),
            'is_read'    => false,
        ]);

        // Notify all admins that the mission is awaiting client validation
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => 'admin_mission_awaiting_validation',
                'title'      => 'Mission en attente de validation client',
                'message'    => "La mission #{$serviceRequest->id} a été marquée terminée par l'artisan. En attente de la confirmation du client.",
                'action_url' => route('admin.missions.index'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Travail marqué comme terminé. Le client doit maintenant confirmer avant que la mission soit officiellement clôturée.');
    }

    /**
     * CLIENT confirms the artisan's work is actually done and satisfactory.
     * This is the action that really closes out the mission — it's the
     * counterpart to complete() above.
     */
    public function validateCompletion(ServiceRequest $serviceRequest): RedirectResponse
    {
        // Guard: only the client who made the request can validate it
        if ($serviceRequest->user_id !== Auth::id()) {
            abort(403);
        }
        if ($serviceRequest->status !== 'awaiting_validation') {
            return back()->with('error', "Il n'y a rien à valider pour le moment.");
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

        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($artisanId) {
            Notification::create([
                'user_id'    => $artisanId,
                'type'       => 'mission_validated_by_client',
                'title'      => 'Mission validée par le client ✅',
                'message'    => "Le client a confirmé que le travail est terminé et satisfaisant.",
                'action_url' => route('user.artisan.service-requests.index'),
                'is_read'    => false,
            ]);
        }

        // Notify all admins about the confirmed completion
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => 'admin_mission_completed',
                'title'      => 'Mission terminée',
                'message'    => "La mission #{$serviceRequest->id} a été validée par le client et est maintenant clôturée.",
                'action_url' => route('admin.missions.index'),
                'is_read'    => false,
            ]);
        }

        return back()->with('success', 'Merci d\'avoir confirmé ! Vous pouvez maintenant laisser un avis à l\'artisan.');
    }

    /**
     * Cancel a service request.
     */
    public function cancel(ServiceRequest $serviceRequest): RedirectResponse
    {
        $artisanId = $serviceRequest->artisan_id ?? $serviceRequest->service?->artisan_id;
        if ($serviceRequest->user_id !== Auth::id() && $artisanId !== Auth::id()) {
            abort(403);
        }

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

    /**
     * Admin view of all service requests.
     */
    public function adminIndex(): View
    {
        $serviceRequests = ServiceRequest::with(['user', 'service.artisan', 'mission'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total'     => ServiceRequest::count(),
            'pending'   => ServiceRequest::where('status', 'pending')->count(),
            'accepted'  => ServiceRequest::where('status', 'accepted')->count(),
            'rejected'  => ServiceRequest::where('status', 'rejected')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
        ];

        return view('admin.service-requests.index', compact('serviceRequests', 'stats'));
    }

    /**
     * Admin responds/updates status of a service request.
     */
    public function adminRespond(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,completed,cancelled',
        ]);

        try {
            $serviceRequest->update([
                'status' => $request->status,
            ]);

            return back()->with('success', 'Statut de la demande mis à jour par l\'administration.');
        } catch (\Throwable $e) {
            \Log::error('Erreur mise à jour admin demande service: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }
}