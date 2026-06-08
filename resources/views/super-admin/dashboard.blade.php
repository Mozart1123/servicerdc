@extends('layouts.super-admin')

@section('page_title', 'Dashboard')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Dashboard</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Overview of ServiceRDC platform activity</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:var(--text-muted);">{{ now()->format('M d, Y · H:i') }}</span>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Invite User
            </a>
        </div>
    </div>

    {{-- ─── STAT CARDS ─── --}}
    <div class="stat-grid">
        {{-- Total Users --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-light);color:var(--accent);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-meta">
                <span style="color:var(--green);font-weight:600;">{{ $stats['active_users'] }} active</span>
                &nbsp;·&nbsp; {{ $stats['suspended_users'] }} suspended
            </div>
        </div>

        {{-- Active Sessions --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:var(--green);">
                <i class="fas fa-wave-square"></i>
            </div>
            <div class="stat-label">Active Sessions</div>
            <div class="stat-value">{{ number_format($stats['active_sessions']) }}</div>
            <div class="stat-meta">Last 30 minutes</div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--amber-bg);color:var(--amber);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-label">Monthly Revenue</div>
            <div class="stat-value">${{ number_format($stats['monthly_revenue'], 2) }}</div>
            <div class="stat-meta">Payment gateway not connected</div>
        </div>

        {{-- System Uptime --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff;color:#a21caf;">
                <i class="fas fa-server"></i>
            </div>
            <div class="stat-label">System Uptime</div>
            <div class="stat-value" style="font-size:20px;">{{ $stats['system_uptime'] }}</div>
            <div class="stat-meta">
                <span style="color:var(--green);font-weight:600;display:inline-flex;align-items:center;gap:5px;">
                    <span
                        style="width:6px;height:6px;border-radius:50%;background:var(--green);display:inline-block;"></span>
                    All systems operational
                </span>
            </div>
        </div>
    </div>

    {{-- ─── SECONDARY STATS ─── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
        <div class="card" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div
                style="width:36px;height:36px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#64748b;border:1px solid var(--border);">
                <i class="fas fa-briefcase" style="font-size:14px;"></i>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Total Services</div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">
                    {{ number_format($stats['total_services']) }}
                </div>
            </div>
        </div>
        <div class="card" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div
                style="width:36px;height:36px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#64748b;border:1px solid var(--border);">
                <i class="fas fa-file-lines" style="font-size:14px;"></i>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Job Offers</div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">
                    {{ number_format($stats['total_jobs']) }}
                </div>
            </div>
        </div>
        <div class="card" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div
                style="width:36px;height:36px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#64748b;border:1px solid var(--border);">
                <i class="fas fa-handshake" style="font-size:14px;"></i>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Active Missions</div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">
                    {{ number_format($stats['active_missions']) }}
                </div>
            </div>
        </div>
        <div class="card" style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div
                style="width:36px;height:36px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#64748b;border:1px solid var(--border);">
                <i class="fas fa-building" style="font-size:14px;"></i>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Organizations</div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">
                    {{ number_format($stats['total_organizations']) }}</div>
            </div>
        </div>
    </div>

    {{-- ─── ACTIVITY + QUICK ACTIONS ─── --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;margin-bottom:24px;">

        {{-- Recent Activity --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Recent Activity</span>
                <a href="{{ route('super-admin.logs') }}"
                    style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:500;">View all</a>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($recentActivity as $item)
                    <div class="activity-item" style="padding:12px 20px;">
                        <div class="activity-icon" style="background:var(--accent-light);color:var(--accent);">
                            @if($item->role === 'super_admin')
                                <i class="fas fa-crown"></i>
                            @elseif($item->role === 'admin')
                                <i class="fas fa-shield-halved"></i>
                            @else
                                <i class="fas fa-user-plus"></i>
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                <strong>{{ $item->name }}</strong> joined as
                                <span
                                    style="color:var(--accent);font-weight:600;">{{ ucfirst(str_replace('_', ' ', $item->role)) }}</span>
                            </div>
                            <div class="activity-time">{{ $item->created_at->diffForHumans() }} · {{ $item->email }}</div>
                        </div>
                        <span class="badge {{ $item->status === 'active' ? 'badge-green' : 'badge-red' }}">
                            <span class="badge-dot"></span>{{ ucfirst($item->status) }}
                        </span>
                    </div>
                @empty
                    <div style="padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px;">No recent activity
                        found.</div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Quick Actions</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('super-admin.users.index') }}" class="quick-action">
                    <div class="qa-icon" style="background:var(--accent-light);color:var(--accent);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <div class="qa-label">Invite User</div>
                        <div class="qa-desc">Add a new team member</div>
                    </div>
                </a>
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:var(--green-bg);color:var(--green);">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="qa-label">Create Plan</div>
                        <div class="qa-desc">Define subscription tiers</div>
                    </div>
                </a>
                <a href="{{ route('super-admin.logs') }}" class="quick-action">
                    <div class="qa-icon" style="background:var(--amber-bg);color:var(--amber);">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <div class="qa-label">View Logs</div>
                        <div class="qa-desc">Check system activity</div>
                    </div>
                </a>
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:#fdf4ff;color:#a21caf;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div>
                        <div class="qa-label">Export Data</div>
                        <div class="qa-desc">Download user/financial CSV</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- ─── USERS TABLE ─── --}}
    <div class="card" x-data="{
            search: '',
            roleFilter: '',
            statusFilter: '',
            sortCol: 'created_at',
            sortDir: 'desc',
            get filtered() {
                return this.$el.querySelectorAll('[data-row]');
            }
        }">
        <div class="card-header">
            <span class="card-title">Recent Users</span>
            <a href="{{ route('super-admin.users.index') }}"
                style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:500;">View all users</a>
        </div>

        {{-- Filter Bar --}}
        <div class="filter-bar" style="flex-wrap:wrap;gap:8px;">
            <div class="filter-input-wrap">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" class="filter-input" placeholder="Search users..." x-model="search"
                    @input="filterTable()">
            </div>
            <select class="filter-select" x-model="roleFilter" @change="filterTable()">
                <option value="">All Roles</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
            </select>
            <select class="filter-select" x-model="statusFilter" @change="filterTable()">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>

        <div class="table-wrap">
            <table id="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr data-row data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}"
                            data-role="{{ $user->role }}" data-status="{{ $user->status }}">
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
                            <td style="color:var(--text-muted);font-size:12px;">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                    {{-- Edit --}}
                                    <button class="btn btn-secondary btn-sm btn-icon" title="Edit user" @click='openEdit({
                                                        id: {{ $user->id }},
                                                        name: {{ json_encode($user->name) }},
                                                        email: {{ json_encode($user->email) }},
                                                        role: {{ json_encode($user->role) }},
                                                        status: {{ json_encode($user->status) }}
                                                    })'>
                                        <i class="fas fa-pen" style="font-size:11px;"></i>
                                    </button>

                                    {{-- Toggle Status --}}
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="/super-admin/users/{{ $user->id }}/toggle-status"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-icon {{ $user->status === 'active' ? 'btn-danger' : 'btn-secondary' }}"
                                                title="{{ $user->status === 'active' ? 'Suspend' : 'Activate' }} user"
                                                onclick="return confirm('{{ $user->status === 'active' ? 'Suspend' : 'Activate' }} {{ addslashes($user->name) }}?')">
                                                <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }}"
                                                    style="font-size:11px;"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        @if(!$user->isSuperAdmin())
                                            <form method="POST" action="{{ route('super-admin.users.destroy', $user->id) }}"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete user"
                                                    onclick="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                                    <i class="fas fa-trash" style="font-size:11px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="pagination">
                <span class="page-info">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of
                    {{ $users->total() }}</span>
                @if($users->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"
                            style="font-size:10px;"></i></a>
                @endif
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @elseif(abs($page - $users->currentPage()) <= 2)
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

    @push('scripts')
        <script>
            function filterTable() {
                const search = document.querySelector('[x-model="search"]')?.value?.toLowerCase() || '';
                const role = document.querySelector('[x-model="roleFilter"]')?.value || '';
                const status = document.querySelector('[x-model="statusFilter"]')?.value || '';

                document.querySelectorAll('[data-row]').forEach(row => {
                    const name = row.dataset.name || '';
                    const email = row.dataset.email || '';
                    const rowRole = row.dataset.role || '';
                    const rowStatus = row.dataset.status || '';

                    const matchSearch = !search || name.includes(search) || email.includes(search);
                    const matchRole = !role || rowRole === role;
                    const matchStatus = !status || rowStatus === status;

                    row.style.display = (matchSearch && matchRole && matchStatus) ? '' : 'none';
                });
            }

            // Hook Alpine inputs when they load
            document.addEventListener('alpine:initialized', () => {
                document.querySelectorAll('.filter-input, .filter-select').forEach(el => {
                    el.addEventListener('input', filterTable);
                    el.addEventListener('change', filterTable);
                });
            });
        </script>
    @endpush

@endsection