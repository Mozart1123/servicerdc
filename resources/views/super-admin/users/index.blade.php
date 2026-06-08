@extends('layouts.super-admin')

@section('page_title', 'Users')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">All Users</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Manage all platform users, roles, and access
            </p>
        </div>
        <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
    </div>

    {{-- ─── STAT ROW ─── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
        @php
            $total = \App\Models\User::count();
            $active = \App\Models\User::where('status', 'active')->count();
            $suspended = \App\Models\User::where('status', 'suspended')->count();
            $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->count();
        @endphp
        <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active</div>
            <div class="stat-value" style="color:var(--green);">{{ $active }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Suspended</div>
            <div class="stat-value" style="color:var(--red);">{{ $suspended }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Admins & Super Admins</div>
            <div class="stat-value" style="color:var(--accent);">{{ $admins }}</div>
        </div>
    </div>

    {{-- ─── USERS TABLE ─── --}}
    <div class="card">
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('super-admin.users.index') }}">
            <div class="filter-bar">
                <div class="filter-input-wrap">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input"
                        placeholder="Search by name or email...">
                </div>
                <select name="role" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-xmark"></i> Clear
                    </a>
                @endif
                <span style="margin-left:auto;font-size:12px;color:var(--text-muted);">{{ $users->total() }}
                    result{{ $users->total() !== 1 ? 's' : '' }}</span>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Joined</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                    <div>
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge badge-blue"><span class="badge-dot"></span>Super Admin</span>
                                @elseif($user->role === 'admin')
                                    <span class="badge badge-amber"><span class="badge-dot"></span>Admin</span>
                                @else
                                    <span class="badge badge-gray"><span class="badge-dot"></span>User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
                                @else
                                    <span class="badge badge-red"><span class="badge-dot"></span>Suspended</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--text-muted);">
                                {{ ucfirst(str_replace('_', ' ', $user->user_type ?? 'client')) }}
                            </td>
                            <td style="font-size:12px;color:var(--text-muted);">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;"
                                    x-data="{ open: false }" @click.outside="open = false">

                                    {{-- Edit Role Button (Opens Slide-over) --}}
                                    <button class="btn btn-secondary btn-sm" @click='openEdit({
                                                id: {{ $user->id }},
                                                name: {{ json_encode($user->name) }},
                                                email: {{ json_encode($user->email) }},
                                                role: {{ json_encode($user->role) }},
                                                status: {{ json_encode($user->status) }}
                                            })'>
                                        <i class="fas fa-pen" style="font-size:11px;"></i> Edit
                                    </button>

                                    {{-- Actions Dropdown --}}
                                    <div class="action-menu">
                                        <button class="btn btn-secondary btn-sm btn-icon" @click="open = !open">
                                            <i class="fas fa-ellipsis" style="font-size:12px;"></i>
                                        </button>
                                        <div class="action-dropdown" x-show="open" x-cloak x-transition>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="/super-admin/users/{{ $user->id }}/toggle-status">
                                                    @csrf
                                                    <button type="submit" class="action-item"
                                                        style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;">
                                                        <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                                        {{ $user->status === 'active' ? 'Suspend User' : 'Activate User' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('super-admin.users.promote', $user->id) }}">
                                                @csrf
                                                <input type="hidden" name="role"
                                                    value="{{ $user->role === 'admin' ? 'user' : 'admin' }}">
                                                <button type="submit" class="action-item"
                                                    style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;">
                                                    <i class="fas fa-arrow-up"></i>
                                                    {{ $user->role === 'admin' ? 'Demote to User' : 'Promote to Admin' }}
                                                </button>
                                            </form>

                                            @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                                <div style="height:1px;background:var(--border);margin:4px 0;"></div>
                                                <form method="POST" action="{{ route('super-admin.users.destroy', $user->id) }}"
                                                    onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-item danger"
                                                        style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;">
                                                        <i class="fas fa-trash"></i> Delete User
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fas fa-users" style="font-size:24px;margin-bottom:10px;display:block;opacity:.3;"></i>
                                No users found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="pagination">
                <span class="page-info">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
                </span>
                @if($users->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"
                            style="font-size:10px;"></i></a>
                @endif

                @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"
                            style="font-size:10px;"></i></a>
                @else
                    <span class="page-btn disabled"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                @endif
            </div>
        @endif
    </div>

@endsection