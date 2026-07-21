<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\PayoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayoutController extends Controller
{
    /**
     * Page "Mes gains" — solde disponible + historique des demandes.
     *
     * Le solde net dû à l'artisan = SUM(amount - commission_amount)
     * sur les missions en payout_status = 'pending_payout'.
     * commission_amount est la part ProConnect ; l'artisan reçoit le reste.
     */
    public function index(): View
    {
        $user = Auth::user();

        // ─── Calcul du solde disponible (montant net dû à l'artisan) ──────────
        $pendingMissions = Mission::where('artisan_id', $user->id)
            ->where('payout_status', 'pending_payout')
            ->get();

        // Net = montant mission - commission ProConnect
        $availableBalance = $pendingMissions->sum(
            fn (Mission $m) => (float) $m->amount - (float) $m->commission_amount
        );

        // ─── Statistiques complémentaires ─────────────────────────────────────
        $totalEarned = Mission::where('artisan_id', $user->id)
            ->where('payout_status', 'paid_out')
            ->get()
            ->sum(fn (Mission $m) => (float) $m->amount - (float) $m->commission_amount);

        $missionsPendingCount = $pendingMissions->count();

        // ─── Historique des demandes de retrait ───────────────────────────────
        $payoutRequests = PayoutRequest::where('artisan_id', $user->id)
            ->latest()
            ->get();

        // ─── Demande en cours (bloque une nouvelle si elle existe) ────────────
        $pendingRequest = PayoutRequest::where('artisan_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('user.gains.index', compact(
            'availableBalance',
            'totalEarned',
            'missionsPendingCount',
            'payoutRequests',
            'pendingRequest',
            'pendingMissions'
        ));
    }

    /**
     * Soumettre une demande de retrait.
     *
     * Validations serveur :
     * - Le montant ne doit pas dépasser le solde net disponible.
     * - Une seule demande en pending autorisée à la fois.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // ─── Vérification : pas de demande pending en cours ───────────────────
        $hasPending = PayoutRequest::where('artisan_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error',
                'Vous avez déjà une demande de retrait en cours de traitement. Attendez qu\'elle soit traitée avant d\'en soumettre une nouvelle.'
            );
        }

        // ─── Calcul du solde disponible (même formule que index()) ───────────
        $availableBalance = Mission::where('artisan_id', $user->id)
            ->where('payout_status', 'pending_payout')
            ->get()
            ->sum(fn (Mission $m) => (float) $m->amount - (float) $m->commission_amount);

        // ─── Validation des champs ────────────────────────────────────────────
        $validated = $request->validate([
            'amount'              => [
                'required',
                'numeric',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($availableBalance) {
                    if ((float) $value > $availableBalance) {
                        $fail(
                            'Le montant demandé (' . number_format((float) $value, 2) . ' $) ' .
                            'dépasse votre solde disponible (' . number_format($availableBalance, 2) . ' $).'
                        );
                    }
                },
            ],
            'mobile_money_number' => ['required', 'string', 'max:30'],
        ], [
            'amount.required'              => 'Le montant est obligatoire.',
            'amount.min'                   => 'Le montant minimum de retrait est de 1 $.',
            'mobile_money_number.required' => 'Le numéro Mobile Money est obligatoire.',
        ]);

        PayoutRequest::create([
            'artisan_id'          => $user->id,
            'amount'              => $validated['amount'],
            'mobile_money_number' => $validated['mobile_money_number'],
            'status'              => 'pending',
        ]);

        return back()->with('success',
            'Votre demande de retrait de ' . number_format((float) $validated['amount'], 2) . ' $ a été soumise. Elle sera traitée dans les plus brefs délais.'
        );
    }
}
