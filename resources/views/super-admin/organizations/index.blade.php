@extends('layouts.super-admin')

@section('page_title', 'Organizations')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Organizations</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Manage platform entities, companies, and
                service groups</p>
        </div>
        <a href="{{ route('super-admin.organizations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Organization
        </a>
    </div>

    {{-- ─── ORGANIZATIONS TABLE ─── --}}
    <div class="card">
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('super-admin.organizations.index') }}">
            <div class="filter-bar">
                <div class="filter-input-wrap">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input"
                        placeholder="Search by name or email...">
                </div>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('super-admin.organizations.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Clear
                    </a>
                @endif
                <span style="margin-left:auto;font-size:12px;color:var(--text-muted);">{{ $organizations->total() }}
                    result{{ $organizations->total() !== 1 ? 's' : '' }}</span>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Joined</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizations as $org)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div
                                        style="width:36px;height:36px;border-radius:8px;background:var(--accent-light);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;">
                                        {{ strtoupper(substr($org->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:var(--text-primary);">{{ $org->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $org->website ?? 'No website' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($org->owner)
                                    <div style="font-size:13px;font-weight:500;">{{ $org->owner->name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $org->owner->email }}</div>
                                @else
                                    <span style="font-size:12px;color:var(--text-muted);font-style:italic;">No owner assigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = match ($org->status) {
                                        'active' => 'badge-green',
                                        'suspended' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}"><span
                                        class="badge-dot"></span>{{ ucfirst($org->status) }}</span>
                            </td>
                            <td>
                                <div style="font-size:12px;color:var(--text-primary);">{{ $org->email ?? 'N/A' }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $org->phone ?? '' }}</div>
                            </td>
                            <td style="font-size:12px;color:var(--text-muted);">
                                {{ $org->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;"
                                    x-data="{ open: false }" @click.outside="open = false">
                                    <a href="{{ route('super-admin.organizations.edit', $org->id) }}"
                                        class="btn btn-secondary btn-sm">
                                        <i class="fas fa-pen" style="font-size:11px;"></i> Edit
                                    </a>

                                    {{-- Actions Dropdown --}}
                                    <div class="action-menu">
                                        <button class="btn btn-secondary btn-sm btn-icon" @click="open = !open">
                                            <i class="fas fa-ellipsis" style="font-size:12px;"></i>
                                        </button>
                                        <div class="action-dropdown" x-show="open" x-cloak x-transition>
                                            <form method="POST"
                                                action="{{ route('super-admin.organizations.toggle-status', $org->id) }}">
                                                @csrf
                                                <button type="submit" class="action-item"
                                                    style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;">
                                                    <i class="fas {{ $org->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                                    {{ $org->status === 'active' ? 'Suspend Organization' : 'Activate Organization' }}
                                                </button>
                                            </form>

                                            <div style="height:1px;background:var(--border);margin:4px 0;"></div>

                                            <form method="POST"
                                                action="{{ route('super-admin.organizations.destroy', $org->id) }}"
                                                onsubmit="return confirm('Permanently delete {{ addslashes($org->name) }}? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-item danger"
                                                    style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;">
                                                    <i class="fas fa-trash"></i> Delete Organization
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
                                <i class="fas fa-building"
                                    style="font-size:32px;margin-bottom:12px;display:block;opacity:.1;"></i>
                                No organizations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($organizations->hasPages())
            <div class="pagination">
                <span class="page-info">
                    Showing {{ $organizations->firstItem() }}–{{ $organizations->lastItem() }} of {{ $organizations->total() }}
                </span>
                @if($organizations->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                @else
                    <a href="{{ $organizations->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"
                            style="font-size:10px;"></i></a>
                @endif

                @foreach($organizations->getUrlRange(max(1, $organizations->currentPage() - 2), min($organizations->lastPage(), $organizations->currentPage() + 2)) as $page => $url)
                    @if($page == $organizations->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($organizations->hasMorePages())
                    <a href="{{ $organizations->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"
                            style="font-size:10px;"></i></a>
                @else
                    <span class="page-btn disabled"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                @endif
            </div>
        @endif
    </div>

@endsection