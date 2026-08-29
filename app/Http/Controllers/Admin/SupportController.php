<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\KpayService;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    public function tickets()
    {
        $tickets = \App\Models\SupportTicket::with('user')->latest()->paginate(20);
        return view('admin.support.tickets', compact('tickets'));
    }

    public function replyTicket(Request $request, $id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);
        $request->validate(['reply' => 'required|string']);

        $ticket->update([
            'admin_reply' => $request->reply,
            'status' => 'pending',
            'replied_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function closeTicket($id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'closed']);
        return response()->json(['success' => true]);
    }

    /**
     * Refund the payment linked to this ticket's mission, via K-PAY.
     *
     * K-PAY only supports full-amount refunds (see KpayService::refundPayment),
     * so there's no partial-amount input here — this always refunds the full
     * confirmed transaction tied to the ticket's mission. The transaction's own
     * status only flips to 'refunded' once K-PAY's refund.completed webhook
     * comes back (see KpayWebhookController::handleTransactionRefund) — this
     * endpoint just *initiates* the refund and records that it was requested.
     */
    public function refundTicket($id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);

        if (!$ticket->mission_id) {
            return response()->json([
                'error' => "Ce ticket n'est lié à aucune mission — il n'y a pas de paiement à rembourser.",
            ], 422);
        }

        $transaction = Transaction::where('mission_id', $ticket->mission_id)
            ->where('status', 'succeeded')
            ->latest()
            ->first();

        if (!$transaction) {
            return response()->json([
                'error' => "Aucun paiement confirmé trouvé pour la mission liée à ce ticket.",
            ], 422);
        }

        if (!$transaction->kpay_reference) {
            return response()->json([
                'error' => "Référence K-PAY introuvable pour ce paiement — remboursement impossible depuis l'admin. Contactez le support K-PAY directement.",
            ], 422);
        }

        try {
            $this->kpayService->refundPayment($transaction->kpay_reference);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Le remboursement a été refusé par K-PAY : ' . $e->getMessage(),
            ], 422);
        }

        $ticket->update([
            'refund_amount' => $transaction->amount,
            'admin_reply'   => trim(($ticket->admin_reply ? $ticket->admin_reply . "\n\n" : '')
                . "Remboursement de {$transaction->amount} {$transaction->currency} initié auprès de K-PAY."),
            'status'        => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Remboursement initié auprès de K-PAY. Le statut sera confirmé automatiquement dès réception de la confirmation.',
        ]);
    }

    public function docs()
    {
        return view('admin.support.docs');
    }

    public function suggestions()
    {
        $suggestions = \App\Models\Suggestion::with('user')->latest()->paginate(20);
        return view('admin.support.suggestions', compact('suggestions'));
    }

    public function toggleSuggestionStatus($id)
    {
        $suggestion = \App\Models\Suggestion::findOrFail($id);
        $nextStatus = $suggestion->status == 'pending' ? 'reviewed' : ($suggestion->status == 'reviewed' ? 'implemented' : 'pending');
        $suggestion->update(['status' => $nextStatus]);
        return response()->json(['success' => true, 'status' => $nextStatus]);
    }
}
