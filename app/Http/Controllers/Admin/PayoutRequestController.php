<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayoutRequestController extends Controller
{
    /**
     * Liste des demandes de retrait en attente.
     * Triée par date croissante (les plus anciennes en premier).
     */
    public function index(): View
    {
        $pendingRequests = PayoutRequest::with('artisan')
            ->where('status', 'pending')
            ->oldest()
            ->get()
            ->map(function (PayoutRequest $req) {
                // Calcul du solde net disponible de l'artisan (amount - commission_amount)
                $req->artisan_available_balance = Mission::where('artisan_id', $req->artisan_id)
                    ->where('payout_status', 'pending_payout')
                    ->get()
                    ->sum(fn (Mission $m) => (float) $m->amount - (float) $m->commission_amount);

                $req->pending_missions_count = Mission::where('artisan_id', $req->artisan_id)
                    ->where('payout_status', 'pending_payout')
                    ->count();

                return $req;
            });

        $processedRequests = PayoutRequest::with(['artisan', 'processor'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('processed_at')
            ->take(30)
            ->get();

        return view('admin.finances.payout-requests', compact('pendingRequests', 'processedRequests'));
    }

    /**
     * Approuver une demande de retrait.
     *
     * - lockForUpdate() sur la PayoutRequest pour éviter les doubles traitements concurrents.
     * - Vérifie que status === 'pending' dans la transaction avant de procéder.
     * - Met à jour les missions pending_payout → paid_out.
     * - Envoie une notification à l'artisan.
     *
     * ATTENTION : ce bouton ne déclenche AUCUN virement automatique.
     * L'admin doit avoir effectué le virement manuellement via K-PAY/opérateur AVANT de cliquer.
     */
    public function approve(Request $request, int $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var PayoutRequest $payout */
                $payout = PayoutRequest::lockForUpdate()->findOrFail($id);

                // Vérification anti-concurrence : si déjà traitée, on annule proprement
                if ($payout->status !== 'pending') {
                    throw new \RuntimeException(
                        'Cette demande a déjà été traitée (statut actuel : ' . $payout->status_label . '). Aucune action effectuée.'
                    );
                }

                // Marquer la demande comme approuvée
                $payout->update([
                    'status'       => 'approved',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);

                // Passer toutes les missions pending_payout de l'artisan à paid_out
                Mission::where('artisan_id', $payout->artisan_id)
                    ->where('payout_status', 'pending_payout')
                    ->update(['payout_status' => 'paid_out']);

                // Notification à l'artisan
                Notification::create([
                    'user_id'      => $payout->artisan_id,
                    'type'         => 'payout_approved',
                    'related_type' => 'payout_request',
                    'related_id'   => $payout->id,
                    'title'        => 'Retrait approuvé ✓',
                    'message'      => 'Votre demande de retrait de ' . number_format((float) $payout->amount, 2) . ' $ a été approuvée. Le virement a été effectué sur votre numéro ' . $payout->mobile_money_number . '.',
                    'action_url'   => route('user.gains.index'),
                    'is_read'      => false,
                ]);
            });

            return redirect()->route('admin.finances.payout-requests.index')
                ->with('success', 'Demande approuvée. Les missions de l\'artisan sont marquées comme payées.');

        } catch (\RuntimeException $e) {
            // Erreur de concurrence ou statut invalide — message clair
            return redirect()->route('admin.finances.payout-requests.index')
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('admin.finances.payout-requests.index')
                ->with('error', 'Une erreur inattendue est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Rejeter une demande de retrait.
     *
     * - Le motif de rejet est obligatoire.
     * - Les missions restent en pending_payout (l'artisan garde son solde).
     * - Notification à l'artisan avec le motif.
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'rejection_reason.required' => 'Le motif de rejet est obligatoire.',
            'rejection_reason.min'      => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                /** @var PayoutRequest $payout */
                $payout = PayoutRequest::lockForUpdate()->findOrFail($id);

                if ($payout->status !== 'pending') {
                    throw new \RuntimeException(
                        'Cette demande a déjà été traitée (statut actuel : ' . $payout->status_label . '). Aucune action effectuée.'
                    );
                }

                $payout->update([
                    'status'           => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'processed_by'     => Auth::id(),
                    'processed_at'     => now(),
                ]);

                // Les missions restent en pending_payout — l'artisan garde son solde

                // Notification à l'artisan
                Notification::create([
                    'user_id'      => $payout->artisan_id,
                    'type'         => 'payout_rejected',
                    'related_type' => 'payout_request',
                    'related_id'   => $payout->id,
                    'title'        => 'Demande de retrait refusée',
                    'message'      => 'Votre demande de retrait de ' . number_format((float) $payout->amount, 2) . ' $ a été refusée. Motif : ' . $request->rejection_reason . ' Votre solde reste disponible pour une nouvelle demande.',
                    'action_url'   => route('user.gains.index'),
                    'is_read'      => false,
                ]);
            });

            return redirect()->route('admin.finances.payout-requests.index')
                ->with('success', 'Demande rejetée. L\'artisan a été notifié avec le motif.');

        } catch (\RuntimeException $e) {
            return redirect()->route('admin.finances.payout-requests.index')
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('admin.finances.payout-requests.index')
                ->with('error', 'Une erreur inattendue est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Détail des missions pending_payout d'un artisan (pour vérification avant approbation).
     */
    public function artisanMissions(int $artisanId): View
    {
        $artisan = User::findOrFail($artisanId);

        $missions = Mission::where('artisan_id', $artisanId)
            ->where('payout_status', 'pending_payout')
            ->with('client')
            ->latest()
            ->get();

        $netBalance = $missions->sum(
            fn (Mission $m) => (float) $m->amount - (float) $m->commission_amount
        );

        return view('admin.finances.artisan-missions', compact('artisan', 'missions', 'netBalance'));
    }
}
