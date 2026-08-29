<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Transaction;
use App\Services\KpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    /**
     * Display all missions with reviews for admin oversight.
     */
    public function index(Request $request): View
    {
        $query = Mission::with(['client', 'artisan', 'service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('artisan', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $missions = $query->paginate(20)->appends($request->query());

        $stats = [
            'total'       => Mission::count(),
            'in_progress' => Mission::inProgress()->count(),
            'completed'   => Mission::completed()->count(),
            'with_review' => Mission::whereNotNull('rating')->count(),
            'avg_rating'  => round(Mission::whereNotNull('rating')->avg('rating') ?? 0, 1),
        ];

        return view('admin.missions.index', compact('missions', 'stats'));
    }

    /**
     * Display a single mission with full review details.
     */
    public function show(Mission $mission): View
    {
        $mission->load(['client', 'artisan', 'service']);

        $transaction = Transaction::where('mission_id', $mission->id)
            ->whereIn('status', ['succeeded', 'refunded'])
            ->latest()
            ->first();

        return view('admin.missions.show', compact('mission', 'transaction'));
    }

    /**
     * Refund the mission's confirmed payment via K-PAY.
     *
     * K-PAY only supports full-amount refunds (see KpayService::refundPayment),
     * so this always refunds the full transaction — there's no partial amount
     * to choose. The transaction's own status only flips to 'refunded' once
     * K-PAY's refund.completed webhook comes back (see
     * KpayWebhookController::handleTransactionRefund) — this action just
     * *initiates* the refund with K-PAY.
     */
    public function refund(Mission $mission): RedirectResponse
    {
        $transaction = Transaction::where('mission_id', $mission->id)
            ->where('status', 'succeeded')
            ->latest()
            ->first();

        if (!$transaction) {
            return back()->with('error', "Aucun paiement confirmé trouvé pour cette mission.");
        }

        if (!$transaction->kpay_reference) {
            return back()->with('error', "Référence K-PAY introuvable pour ce paiement — remboursement impossible depuis l'admin. Contactez le support K-PAY directement.");
        }

        try {
            $this->kpayService->refundPayment($transaction->kpay_reference);
        } catch (\Exception $e) {
            return back()->with('error', 'Le remboursement a été refusé par K-PAY : ' . $e->getMessage());
        }

        return back()->with('success', 'Remboursement initié auprès de K-PAY. Le statut sera confirmé automatiquement dès réception de la confirmation.');
    }
}
