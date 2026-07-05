<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mission;
use App\Models\Setting;
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
        $query = Mission::with(['client', 'artisan']);
        
        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->get();
        return view('admin.finances.transactions', compact('transactions'));
    }

    public function exportTransactions(Request $request)
    {
        $status = $request->get('status');
        $query = Mission::with(['client', 'artisan']);
        
        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->get();
        $filename = 'transactions_export_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Référence', 'Date', 'Client', 'Artisan', 'Montant', 'Statut']);

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    '#TRX-' . $trx->id,
                    $trx->created_at->format('Y-m-d H:i'),
                    $trx->client?->name ?? 'N/A',
                    $trx->artisan?->name ?? 'N/A',
                    $trx->amount . '$',
                    $trx->status_label
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function commissions()
    {
        $commissionRate = Setting::where('key', 'commission_rate')->first()?->value ?? 15;
        return view('admin.finances.commissions', compact('commissionRate'));
    }

    public function updateCommission(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'commission_rate'],
            ['value' => $request->rate]
        );

        return response()->json([
            'success' => true,
            'message' => 'Taux de commission mis à jour avec succès.',
            'new_rate' => $request->rate
        ]);
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
