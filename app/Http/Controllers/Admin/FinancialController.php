<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mission;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Withdrawal;
use App\Services\KpayService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialController extends Controller
{
    protected $kpayService;

    public function __construct(KpayService $kpayService)
    {
        $this->kpayService = $kpayService;
    }

    public function dashboard()
    {
        // 1. K-PAY Wallet Balance
        try {
            $wallets = $this->kpayService->getBalance();
            // Ensure we always have an array (API may return null)
            if (!is_array($wallets)) {
                $wallets = [];
            }
            $wallet = [
                'wallets' => $wallets,
                'balance' => collect($wallets)->sum('balance'),
                'available' => collect($wallets)->sum('availableBalance'),
                'currency' => 'MULTI',
            ];
        } catch (\Exception $e) {
            $wallet = ['wallets' => [], 'balance' => 0, 'available' => 0, 'currency' => 'N/A', 'error' => $e->getMessage()];
        }

        // 2. Revenue Statistics
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'revenue_today' => Transaction::where('status', 'succeeded')->whereDate('created_at', $today)->sum('amount'),
            'revenue_week' => Transaction::where('status', 'succeeded')->where('created_at', '>=', $thisWeek)->sum('amount'),
            'revenue_month' => Transaction::where('status', 'succeeded')->where('created_at', '>=', $thisMonth)->sum('amount'),
            'total_revenue' => Transaction::where('status', 'succeeded')->sum('amount'),
            'active_subs' => Subscription::where('status', 'active')->count(),
            'expired_subs' => Subscription::where('status', 'expired')->count(),
            'pending_payments' => Transaction::where('status', 'pending')->count(),
            'success_payments' => Transaction::where('status', 'succeeded')->count(),
            'failed_payments' => Transaction::where('status', 'failed')->count(),
        ];

        return view('admin.finances.dashboard', compact('wallet', 'stats'));
    }

    public function withdrawals()
    {
        $withdrawals = Withdrawal::latest()->paginate(15);
        return view('admin.finances.withdraw-history', compact('withdrawals'));
    }

    public function withdraw()
    {
        $wallet = ['balance' => 0, 'available' => 0, 'currency' => 'USD'];
        try {
            $wallets = $this->kpayService->getBalance();
            $walletData = $wallets[0] ?? [];
            if (!empty($walletData)) {
                $wallet['balance'] = $walletData['balance'] ?? 0;
                $wallet['available'] = $walletData['availableBalance'] ?? 0;
                $wallet['currency'] = $walletData['currency'] ?? 'USD';
            }
        } catch (\Exception $e) {}

        return view('admin.finances.withdraw', compact('wallet'));
    }

    public function processWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'provider' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $referenceId = 'WD-' . Str::uuid()->toString();

        // Save local withdrawal record as pending
        $withdrawal = Withdrawal::create([
            'amount' => $request->amount,
            'provider' => $request->provider,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
            'reference_id' => $referenceId,
        ]);

        try {
            // Send request to K-PAY
            $response = $this->kpayService->initiatePayout([
                'amount' => $request->amount,
                'provider' => $request->provider,
                'phoneNumber' => $request->phone_number,
                'externalId' => $referenceId,
            ]);

            $withdrawal->kpay_reference = $response['paymentId'] ?? null;
            $withdrawal->save();

            return redirect()->route('admin.finances.withdrawals')->with('success', 'Retrait initié avec succès. En attente de confirmation.');
        } catch (\Exception $e) {
            $withdrawal->status = 'failed';
            $withdrawal->notes = $e->getMessage();
            $withdrawal->save();
            
            return back()->with('error', 'Erreur lors de l\'initiation du retrait : ' . $e->getMessage());
        }
    }
    public function transactions(Request $request)
    {
        $status = $request->get('status');
        $query = Transaction::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->get();
        $this->attachSubscriptionEndDates($transactions);

        // HUD cards: real data over the last 30 days (was hard-coded before).
        // "Volume Total" is scoped to USD transactions only — Transaction
        // records mix currencies (USD, CDF, ...) depending on the K-PAY
        // provider used, and summing across currencies would be meaningless.
        $since        = now()->subDays(30);
        $totalVolume  = Transaction::where('status', 'succeeded')->where('currency', 'USD')->where('created_at', '>=', $since)->sum('amount');
        $totalCount   = Transaction::where('created_at', '>=', $since)->count();
        $successCount = Transaction::where('status', 'succeeded')->where('created_at', '>=', $since)->count();
        $successRate  = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 1) : 0;

        return view('admin.finances.transactions', compact('transactions', 'totalVolume', 'successRate'));
    }

    public function exportTransactions(Request $request)
    {
        $status = $request->get('status');
        $query = Transaction::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->get();
        $this->attachSubscriptionEndDates($transactions);
        $filename = 'transactions_export_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Référence', 'Date', 'Client / Artisan', 'Type', 'Montant', 'Devise', 'Fin abonnement', 'Statut']);

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $trx->reference_id ?? ('#TRX-' . $trx->id),
                    $trx->created_at->format('Y-m-d H:i'),
                    $trx->user?->name ?? 'N/A',
                    $trx->type,
                    $trx->amount,
                    $trx->currency,
                    $trx->subscription_end?->format('Y-m-d') ?? '',
                    $trx->status,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * For every subscription-type transaction in the collection, resolve and
     * attach the related Subscription's end date as a $transaction->subscription_end
     * dynamic property — batched into a single query rather than one per row.
     *
     * A Transaction has no direct subscription_id, so the match is made via
     * Subscription.transaction_ref, which gets stamped with either the K-PAY
     * reference or our own reference_id depending on when it was set
     * (see KpayWebhookController::fulfillSubscription()) — both are checked.
     */
    private function attachSubscriptionEndDates($transactions): void
    {
        $subRefs = $transactions->where('type', 'subscription')
            ->flatMap(fn ($t) => array_filter([$t->kpay_reference, $t->reference_id]))
            ->unique()
            ->values();

        $subscriptionsByRef = $subRefs->isEmpty()
            ? collect()
            : Subscription::whereIn('transaction_ref', $subRefs)->get()->keyBy('transaction_ref');

        $transactions->each(function ($t) use ($subscriptionsByRef) {
            $t->subscription_end = $t->type === 'subscription'
                ? ($subscriptionsByRef->get($t->kpay_reference) ?? $subscriptionsByRef->get($t->reference_id))?->ends_at
                : null;
        });
    }

    public function invoicing()
    {
        $invoices = Mission::with(['client', 'artisan'])
            ->where('status', 'completed')
            ->latest()
            ->get();
            
        return view('admin.finances.invoicing', compact('invoices'));
    }

    public function exportInvoices()
    {
        $invoices = Mission::with(['client', 'artisan'])
            ->where('status', 'completed')
            ->latest()
            ->get();
            
        $filename = 'factures_export_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['N° Facture', 'Date', 'Destinataire', 'Montant HT']);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    '#INV-2026-' . str_pad($invoice->id, 3, '0', STR_PAD_LEFT),
                    $invoice->updated_at->format('Y-m-d'),
                    $invoice->client?->name ?? 'N/A',
                    $invoice->amount . '$'
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
