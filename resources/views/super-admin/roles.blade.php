@extends('layouts.super-admin')

@section('page_title', 'Roles & Permissions')

@section('content')

    {{-- ─── HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Roles & Permissions
            </h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Understand and manage what each role can do on
                ServiceRDC</p>
        </div>
    </div>

    {{-- ─── ROLE CARDS ─── --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;">
        @foreach($roleSummary as $roleKey => $role)
            @php
                $accentMap = ['blue' => 'var(--accent)', 'amber' => 'var(--amber)', 'gray' => '#64748b'];
                $bgMap = ['blue' => 'var(--accent-light)', 'amber' => 'var(--amber-bg)', 'gray' => '#f1f5f9'];
                $accent = $accentMap[$role['color']] ?? 'var(--accent)';
                $bg = $bgMap[$role['color']] ?? 'var(--accent-light)';
            @endphp
            <div class="card" style="overflow:hidden;">
                {{-- Card Header --}}
                <div style="padding:20px;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <span
                            style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;background:{{ $bg }};color:{{ $accent }};font-size:12px;font-weight:700;">
                            @if($roleKey === 'super_admin') <i class="fas fa-crown"></i>
                            @elseif($roleKey === 'admin') <i class="fas fa-shield-halved"></i>
                            @else <i class="fas fa-user"></i>
                            @endif
                            {{ $role['label'] }}
                        </span>
                        <span style="font-size:22px;font-weight:700;color:{{ $accent }};">{{ $role['count'] }}</span>
                    </div>
                    <p style="font-size:12px;color:var(--text-muted);line-height:1.5;">{{ $role['description'] }}</p>
                </div>

                {{-- Permissions List --}}
                <div style="padding:16px 20px;">
                    <p
                        style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:10px;">
                        Permissions</p>
                    <ul style="list-style:none;display:flex;flex-direction:column;gap:7px;">
                        @foreach($role['permissions'] as $perm)
                            <li style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-primary);">
                                <i class="fas fa-check" style="font-size:10px;color:{{ $accent }};"></i>
                                {{ $perm }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Members --}}
                @if($role['users']->count())
                    <div style="padding:12px 20px;border-top:1px solid var(--border);background:var(--bg-main);">
                        <p
                            style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:8px;">
                            Members</p>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            @foreach($role['users'] as $u)
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div
                                        style="width:24px;height:24px;border-radius:50%;background:{{ $bg }};color:{{ $accent }};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div style="min-width:0;">
                                        <div
                                            style="font-size:12px;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            {{ $u->name }}</div>
                                        <div
                                            style="font-size:11px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            {{ $u->email }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ─── ALL USERS TABLE WITH ROLE MANAGEMENT ─── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Manage User Roles</span>
            <span style="font-size:12px;color:var(--text-muted);">{{ $allUsers->total() }} users total</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Current Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allUsers as $user)
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
                            <td style="font-size:12px;color:var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('super-admin.roles.update', $user->id) }}"
                                        style="display:flex;align-items:center;gap:8px;">
                                        @csrf
                                        <select name="role" class="filter-select" style="height:30px;font-size:12px;">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super
                                                Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm">Apply</button>
                                    </form>
                                @else
                                    <span style="font-size:12px;color:var(--text-muted);font-style:italic;">Your account</span>
                                @endif
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
        @if($allUsers->hasPages())
            <div class="pagination">
                <span class="page-info">Showing {{ $allUsers->firstItem() }}–{{ $allUsers->lastItem() }} of
                    {{ $allUsers->total() }}</span>
                @if($allUsers->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                @else
                    <a href="{{ $allUsers->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"
                            style="font-size:10px;"></i></a>
                @endif
                @foreach($allUsers->getUrlRange(max(1, $allUsers->currentPage() - 2), min($allUsers->lastPage(), $allUsers->currentPage() + 2)) as $page => $url)
                    @if($page == $allUsers->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($allUsers->hasMorePages())
                    <a href="{{ $allUsers->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"
                            style="font-size:10px;"></i></a>
                @else
                    <span class="page-btn disabled"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                @endif
            </div>
        @endif
    </div>

@endsection