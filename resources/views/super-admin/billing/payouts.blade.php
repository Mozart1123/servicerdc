@extends('layouts.super-admin')

@section('page_title', 'Payouts Ledger')

@section('content')

    {{-- ─── STATS GRID ─── --}}
    <div class="stat-grid" style="margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--amber-bg);color:var(--amber);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Pending Balance</div>
            <div class="stat-value">${{ number_format($pendingBalance, 2) }}</div>
            <div class="stat-meta">Awaiting transfer</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-bg);color:var(--green);">
                <i class="fas fa-money-bill-trend-up"></i>
            </div>
            <div class="stat-label">Total Paid Out</div>
            <div class="stat-value">${{ number_format($totalPaidOut, 2) }}</div>
            <div class="stat-meta" style="color:var(--green);"><i class="fas fa-arrow-up"></i> Lifetime payouts</div>
        </div>
    </div>

    {{-- ─── FILTER BAR ─── --}}
    <div class="card" style="margin-bottom:24px;">
        <form method="GET" action="{{ route('super-admin.billing.payouts') }}" class="filter-bar">
            <div class="filter-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search org or ref ID..."
                    class="filter-input">
            </div>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
            </select>

            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('super-admin.billing.payouts') }}"
                    style="font-size:13px;color:var(--text-muted);text-decoration:none;margin-left:auto;">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- ─── PAYOUTS TABLE ─── --}}
    <div class="card">
        @if($payouts->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Created</th>
                            <th>Reference ID</th>
                            <th>Organization / Account</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-weight:600;">{{ $payout->created_at->format('M d, Y') }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">
                                        {{ $payout->created_at->format('H:i:s A') }}</div>
                                </td>
                                <td>
                                    <span
                                        style="font-family:monospace;font-size:12px;background:var(--bg-main);padding:2px 6px;border-radius:4px;color:var(--text-secondary);">
                                        {{ $payout->reference_id ?? 'PAY-' . $payout->id }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:600;">
                                        {{ optional($payout->organization)->name ?? 'Unknown Organization' }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);"><i class="fas fa-bank"></i>
                                        {{ ucfirst(str_replace('_', ' ', $payout->method)) }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:700;font-size:14px;">${{ number_format($payout->amount, 2) }} <span
                                            style="font-size:11px;color:var(--text-muted);font-weight:500;">{{ strtoupper($payout->currency) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($payout->status == 'paid')
                                        <span class="badge badge-green">
                                            <div class="badge-dot"></div> Paid
                                        </span>
                                    @elseif($payout->status == 'failed' || $payout->status == 'canceled')
                                        <span class="badge badge-red">
                                            <div class="badge-dot"></div> {{ ucfirst($payout->status) }}
                                        </span>
                                    @elseif($payout->status == 'processing')
                                        <span class="badge badge-accent">
                                            <div class="badge-dot" style="background:var(--accent);animation:pulse 2s infinite;"></div>
                                            Processing
                                        </span>
                                    @else
                                        <span class="badge badge-amber">
                                            <div class="badge-dot"></div> Pending
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <button class="icon-btn" title="Process Payout"
                                        onclick="alert('Payout processing interface coming soon!')"><i
                                            class="fas fa-paper-plane"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--border);">
                    {{ $payouts->links() }}
                </div>
            @endif
        @else
            <div style="padding:100px 20px;text-align:center;">
                <div
                    style="width:80px;height:80px;background:var(--bg-main);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--text-muted);font-size:32px;">
                    <i class="fas fa-money-bill-transfer"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">No payouts requested
                </h3>
                <p style="font-size:14px;color:var(--text-muted);max-width:300px;margin:0 auto;">When organizations earn revenue
                    and request withdrawals, they will appear here.</p>
            </div>
        @endif
    </div>

@endsection