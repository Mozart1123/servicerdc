<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mission;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialController extends Controller
{
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
