<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Payout;
use App\Models\Plan;

class BillingController extends Controller
{
    /**
     * Overview of platform revenue and active subscriptions
     */
    public function index()
    {
        // 1. Total Revenue (Successful transactions)
        $totalRevenue = Transaction::where('status', 'succeeded')->sum('amount');

        // 2. Active Subscriptions count
        $activeSubscriptionsCount = Subscription::where('status', 'active')->count();

        // 3. MRR (Monthly Recurring Revenue) estimation (Sum of active plans prices)
        // For simplicity, we join with plans table
        $mrr = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price');

        // 4. Recent Transactions
        $recentTransactions = Transaction::with('organization')
            ->latest()
            ->take(5)
            ->get();

        // 5. Active Subscriptions List
        $subscriptions = Subscription::with(['organization', 'plan'])
            ->where('status', 'active')
            ->latest()
            ->paginate(10);

        return view('super-admin.billing.index', compact(
            'totalRevenue',
            'activeSubscriptionsCount',
            'mrr',
            'recentTransactions',
            'subscriptions'
        ));
    }

    /**
     * Ledger of all transactions/invoices across the platform
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with('organization');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('organization', function ($orgQuery) use ($search) {
                        $orgQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.billing.transactions', compact('transactions'));
    }

    /**
     * Ledger for requesting and tracking payouts to organizations
     */
    public function payouts(Request $request)
    {
        $query = Payout::with('organization');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                    ->orWhereHas('organization', function ($orgQuery) use ($search) {
                        $orgQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Stats
        $totalPaidOut = Payout::where('status', 'paid')->sum('amount');
        $pendingBalance = Payout::whereIn('status', ['pending', 'processing'])->sum('amount');

        $payouts = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.billing.payouts', compact('payouts', 'totalPaidOut', 'pendingBalance'));
    }
}
