@extends('layouts.super-admin')

@section('page_title', 'Billing & Financial Overview')

@section('content')

    {{-- ─── STATS GRID ─── --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-bg);color:var(--green);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-meta" style="color:var(--green);"><i class="fas fa-arrow-up"></i> Lifetime successful</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-light);color:var(--accent);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-label">Estimated MRR</div>
            <div class="stat-value">${{ number_format($mrr, 2) }}</div>
            <div class="stat-meta">Monthly recurring revenue</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:var(--amber-bg);color:var(--amber);">
                <i class="fas fa-building-circle-check"></i>
            </div>
            <div class="stat-label">Active Subscriptions</div>
            <div class="stat-value">{{ number_format($activeSubscriptionsCount) }}</div>
            <div class="stat-meta">Organizations on paid plans</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:var(--red-bg);color:var(--red);">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-label">ARPU</div>
            <div class="stat-value">
                ${{ $activeSubscriptionsCount > 0 ? number_format($mrr / $activeSubscriptionsCount, 2) : '0.00' }}</div>
            <div class="stat-meta">Average revenue per user</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px;">
        {{-- ─── ACTIVE SUBSCRIPTIONS TABLE ─── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Active Subscriptions</h2>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            @if($subscriptions->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Started</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $sub)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;">{{ $sub->organization->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);"><i class="fas fa-link"></i>
                                            {{ $sub->organization->website ?? 'No website' }}</div>
                                    </td>
                                    <td>
                                        <span style="font-weight:600;color:var(--accent);">{{ $sub->plan->name }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">${{ number_format($sub->plan->price, 2) }} <span
                                                style="font-size:11px;color:var(--text-muted);font-weight:400;">/mo</span></div>
                                    </td>
                                    <td>
                                        <span class="badge badge-green">
                                            <div class="badge-dot"></div> Active
                                        </span>
                                    </td>
                                    <td style="color:var(--text-muted);font-size:12px;">
                                        {{ $sub->starts_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding:60px 20px;text-align:center;">
                    <div
                        style="width:64px;height:64px;background:var(--bg-main);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--text-muted);font-size:24px;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 style="font-size:16px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">No active
                        subscriptions</h3>
                    <p style="font-size:14px;color:var(--text-muted);">When organizations subscribe to a paid plan, they will
                        appear here.</p>
                </div>
            @endif
            @if($subscriptions->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--border);">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>

        {{-- ─── RECENT TRANSACTIONS ─── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Transactions</h2>
                <a href="{{ route('super-admin.billing.transactions') }}" class="btn btn-secondary btn-sm">Ledger</a>
            </div>
            <div style="padding:10px 20px;">
                @forelse($recentTransactions as $tx)
                    <div class="activity-item">
                        <div class="activity-icon"
                            style="background:{{ $tx->status == 'succeeded' ? 'var(--green-bg)' : 'var(--red-bg)' }};color:{{ $tx->status == 'succeeded' ? 'var(--green)' : 'var(--red)' }};">
                            <i class="fas {{ $tx->status == 'succeeded' ? 'fa-arrow-down' : 'fa-xmark' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div class="activity-text">
                                    <strong>{{ optional($tx->organization)->name ?? 'Unknown Organization' }}</strong>
                                </div>
                                <div
                                    style="font-weight:700;font-size:14px;color:{{ $tx->status == 'succeeded' ? 'var(--text-primary)' : 'var(--text-muted)' }};">
                                    +${{ number_format($tx->amount, 2) }}
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2px;">
                                <div class="activity-time">{{ $tx->description ?? 'Subscription payment' }}</div>
                                <div class="activity-time">{{ $tx->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding:40px 0;text-align:center;color:var(--text-muted);font-size:13px;">
                        No recent transactions.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection