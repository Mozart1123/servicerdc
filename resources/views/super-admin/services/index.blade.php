@extends('layouts.super-admin')

@section('page_title', 'Service Moderation')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Service Moderation
            </h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Review, verify, and manage all artisan
                services on the platform</p>
        </div>
    </div>

    {{-- ─── SERVICES TABLE ─── --}}
    <div class="card">
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('super-admin.services.index') }}">
            <div class="filter-bar">
                <div class="filter-input-wrap">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input"
                        placeholder="Search by service title or artisan...">
                </div>

                <select name="verified" class="filter-select" onchange="this.form.submit()">
                    <option value="">Verification Status</option>
                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified Only</option>
                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified Only</option>
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'verified']))
                    <a href="{{ route('super-admin.services.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Clear
                    </a>
                @endif

                <span style="margin-left:auto;font-size:12px;color:var(--text-muted);">{{ $services->total() }} services
                    found</span>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Artisan</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div
                                        style="width:40px;height:40px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid var(--border);">
                                        @if($service->images && count($service->images) > 0)
                                            <img src="{{ Str::startsWith($service->images[0], 'http') ? $service->images[0] : asset('storage/' . $service->images[0]) }}"
                                                style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <i class="fas fa-tools" style="color:var(--text-muted);font-size:14px;"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:var(--text-primary);font-size:13px;">
                                            {{ $service->title }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">
                                            {{ number_format($service->price, 2) }} {{ config('app.currency', 'USD') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($service->artisan)
                                    <div style="font-size:13px;font-weight:500;">{{ $service->artisan->name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $service->artisan->email }}</div>
                                @else
                                    <span style="font-size:12px;color:var(--text-muted);font-style:italic;">Unknown Artisan</span>
                                @endif
                            </td>
                            <td>
                                <span
                                    style="font-size:12px;padding:3px 8px;background:var(--bg-light);border:1px solid var(--border);border-radius:4px;color:var(--text-secondary);">
                                    {{ $service->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $service->status === 'active' ? 'badge-green' : 'badge-red' }}">
                                    <span class="badge-dot"></span> {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td>
                                @if($service->is_verified)
                                    <span
                                        style="display:inline-flex;align-items:center;gap:4px;color:var(--accent);font-size:12px;font-weight:600;">
                                        <i class="fas fa-circle-check"></i> Verified
                                    </span>
                                @else
                                    <span
                                        style="display:inline-flex;align-items:center;gap:4px;color:var(--text-muted);font-size:12px;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;"
                                    x-data="{ open: false }" @click.outside="open = false">
                                    {{-- Verify Toggle --}}
                                    <form method="POST"
                                        action="{{ route('super-admin.services.toggle-verification', $service->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn {{ $service->is_verified ? 'btn-secondary' : 'btn-primary' }} btn-sm"
                                            title="{{ $service->is_verified ? 'Unverify Service' : 'Verify Service' }}">
                                            <i class="fas {{ $service->is_verified ? 'fa-xmark' : 'fa-check' }}"
                                                style="font-size:11px;"></i>
                                            {{ $service->is_verified ? 'Unverify' : 'Verify' }}
                                        </button>
                                    </form>

                                    {{-- Actions Dropdown --}}
                                    <div class="action-menu">
                                        <button class="btn btn-secondary btn-sm btn-icon" @click="open = !open">
                                            <i class="fas fa-ellipsis" style="font-size:12px;"></i>
                                        </button>
                                        <div class="action-dropdown" x-show="open" x-cloak x-transition>
                                            <form method="POST"
                                                action="{{ route('super-admin.services.toggle-status', $service->id) }}">
                                                @csrf
                                                <button type="submit" class="action-item"
                                                    style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;">
                                                    <i
                                                        class="fas {{ $service->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                                    {{ $service->status === 'active' ? 'Suspend Service' : 'Activate Service' }}
                                                </button>
                                            </form>

                                            <div style="height:1px;background:var(--border);margin:4px 0;"></div>

                                            <form method="POST"
                                                action="{{ route('super-admin.services.destroy', $service->id) }}"
                                                onsubmit="return confirm('Permanently remove {{ addslashes($service->title) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-item danger"
                                                    style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;">
                                                    <i class="fas fa-trash"></i> Delete Service
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:60px;color:var(--text-muted);">
                                <i class="fas fa-wrench"
                                    style="font-size:32px;margin-bottom:12px;display:block;opacity:.1;"></i>
                                No services found matches your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($services->hasPages())
            <div class="pagination">
                <span class="page-info">
                    Showing {{ $services->firstItem() }}–{{ $services->lastItem() }} of {{ $services->total() }}
                </span>
                @if($services->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"
                            style="font-size:10px;"></i></a>
                @endif

                @foreach($services->getUrlRange(max(1, $services->currentPage() - 2), min($services->lastPage(), $services->currentPage() + 2)) as $page => $url)
                    @if($page == $services->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"
                            style="font-size:10px;"></i></a>
                @else
                    <span class="page-btn disabled"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                @endif
            </div>
        @endif
    </div>

@endsection