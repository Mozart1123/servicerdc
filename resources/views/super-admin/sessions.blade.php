@extends('layouts.super-admin')

@section('page_title', 'Active Sessions')

@section('content')

    {{-- ─── HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Active Sessions</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Monitor real-time user activity and active
                connections</p>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <span class="badge badge-green" style="padding:6px 12px;">
                <span class="badge-dot"></span> {{ $activeSessions }} Active Now
            </span>
        </div>
    </div>

    {{-- ─── SESSIONS TABLE ─── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Recent Activity Log</span>
            <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Last 50
                entries</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Last Activity</th>
                        <th style="text-align:right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessionData as $session)
                        @php
                            $user = $session->user_id ? \App\Models\User::find($session->user_id) : null;
                            $isRecent = (time() - $session->last_activity) < 1800; // 30 mins
                        @endphp
                        <tr>
                            <td>
                                @if($user)
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email" style="font-size:11px;">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div style="display:flex;align-items:center;gap:10px;color:var(--text-muted);">
                                        <div
                                            style="width:32px;height:32px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-user-secret" style="font-size:12px;"></i>
                                        </div>
                                        <span style="font-size:13px;font-style:italic;">Guest / Anonymous</span>
                                    </div>
                                @endif
                            </td>
                            <td style="font-family:monospace;font-size:12px;color:var(--text-secondary);">
                                {{ $session->ip_address }}
                            </td>
                            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:var(--text-muted);"
                                title="{{ $session->user_agent }}">
                                {{ $session->user_agent }}
                            </td>
                            <td style="font-size:12px;color:var(--text-secondary);">
                                {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                            </td>
                            <td style="text-align:right;">
                                @if($isRecent)
                                    <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
                                @else
                                    <span class="badge badge-gray"><span class="badge-dot"></span>Idle</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:60px;color:var(--text-muted);">
                                <i class="fas fa-clock-rotate-left"
                                    style="font-size:32px;margin-bottom:12px;display:block;opacity:.1;"></i>
                                No active session data found in the database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-success" style="margin-top:20px;background:#eff6ff;border-color:#dbeafe;color:#1e40af;">
        <i class="fas fa-circle-info"></i>
        <strong>Note:</strong> Session monitoring requires the <code>database</code> session driver to be enabled in your
        <code>.env</code> file.
    </div>

@endsection