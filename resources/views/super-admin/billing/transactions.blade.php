@extends('layouts.super-admin')

@section('page_title', 'Transactions Ledger')

@section('content')

    {{-- ─── FILTER BAR ─── --}}
    <div class="card" style="margin-bottom:24px;">
        <form method="GET" action="{{ route('super-admin.billing.transactions') }}" class="filter-bar">
            <div class="filter-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search org, ref ID, or description..." class="filter-input">
            </div>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>Succeeded</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('super-admin.billing.transactions') }}"
                    style="font-size:13px;color:var(--text-muted);text-decoration:none;margin-left:auto;">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- ─── TRANSACTIONS TABLE ─── --}}
    <div class="card">
        @if($transactions->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>Reference ID</th>
                            <th>Customer / Org</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-weight:600;">{{ $tx->created_at->format('M d, Y') }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $tx->created_at->format('H:i:s A') }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        style="font-family:monospace;font-size:12px;background:var(--bg-main);padding:2px 6px;border-radius:4px;color:var(--text-secondary);">
                                        {{ $tx->reference_id ?? 'SYS-GEN-' . $tx->id }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:600;">{{ optional($tx->organization)->name ?? 'Unknown Organization' }}
                                    </div>
                                </td>
                                <td
                                    style="color:var(--text-secondary);font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $tx->description ?? 'Subscription charge' }}
                                </td>
                                <td>
                                    <div style="font-weight:700;font-size:14px;">${{ number_format($tx->amount, 2) }} <span
                                            style="font-size:11px;color:var(--text-muted);font-weight:500;">{{ strtoupper($tx->currency) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($tx->status == 'succeeded')
                                        <span class="badge badge-green">
                                            <div class="badge-dot"></div> Succeeded
                                        </span>
                                    @elseif($tx->status == 'failed')
                                        <span class="badge badge-red">
                                            <div class="badge-dot"></div> Failed
                                        </span>
                                    @elseif($tx->status == 'refunded')
                                        <span class="badge badge-gray">
                                            <div class="badge-dot"></div> Refunded
                                        </span>
                                    @else
                                        <span class="badge badge-amber">
                                            <div class="badge-dot"></div> Pending
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <button class="icon-btn" title="View Receipt"><i class="fas fa-file-invoice"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--border);">
                    {{ $transactions->links() }}
                </div>
            @endif
        @else
            <div style="padding:100px 20px;text-align:center;">
                <div
                    style="width:80px;height:80px;background:var(--bg-main);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--text-muted);font-size:32px;">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">No transactions found
                </h3>
                <p style="font-size:14px;color:var(--text-muted);max-width:300px;margin:0 auto;">Payments and refunds will
                    appear in this ledger once organizations start transacting.</p>
            </div>
        @endif
    </div>

@endsection