@extends('layouts.super-admin')

@section('page_title', 'Advanced Audit Trail')

@section('content')

    <style>
        .log-row {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid var(--border-light);
        }

        .log-row:hover {
            background: var(--bg-light);
        }

        .log-row.expanded {
            background: #f8fafc;
        }

        .detail-ghost {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 11px;
            color: var(--text-muted);
        }

        .badge-outline {
            border: 1px solid var(--border);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            background: white;
        }

        pre.payload-box {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.6;
            overflow-x: auto;
            margin: 0;
        }

        .resource-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px dashed transparent;
            transition: all 0.2s;
        }

        .resource-link:hover {
            border-bottom-color: var(--accent);
        }
    </style>

    {{-- ─── PAGE HEADER ✨ ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;">Advanced Audit Trail
            </h1>
            <p style="font-size:14px;color:var(--text-muted);margin-top:4px;">Deep system forensics and behavioral
                monitoring</p>
        </div>
        <div style="display:flex;gap:12px;">
            <form method="POST" action="{{ route('super-admin.logs.clear') }}"
                onsubmit="return confirm('Archive and clear all current logs?')">
                @csrf
                <button type="submit" class="btn btn-secondary" style="border-radius:10px;color:var(--danger);">
                    <i class="fas fa-trash-sweep" style="margin-right:8px;"></i> Purge History
                </button>
            </form>
            <button class="btn btn-primary" style="border-radius:10px;">
                <i class="fas fa-file-export" style="margin-right:8px;"></i> Generate CSV
            </button>
        </div>
    </div>

    {{-- ─── FILTER STACK 🧱 ─── --}}
    <div class="card" style="padding:16px;margin-bottom:32px;border-radius:16px;">
        <form method="GET" action="{{ route('super-admin.logs') }}"
            style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;">
            <div style="flex:1;min-width:300px;position:relative;">
                <i class="fas fa-search"
                    style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search actions, users, emails, or sessions..."
                    style="width:100%;padding:10px 16px 10px 44px;border-radius:10px;border:1px solid var(--border);background:var(--bg-light);font-size:14px;outline:none;">
            </div>

            <select name="severity" onchange="this.form.submit()"
                style="padding:10px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-light);font-size:14px;outline:none;">
                <option value="">Any Severity</option>
                <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="danger" {{ request('severity') == 'danger' ? 'selected' : '' }}>Danger</option>
            </select>

            @if(request()->anyFilled(['search', 'severity']))
                <a href="{{ route('super-admin.logs') }}"
                    style="font-size:13px;color:var(--text-muted);text-decoration:none;">Reset</a>
            @endif
        </form>
    </div>

    {{-- ─── AUDIT TABLE 🛡️ ─── --}}
    @if($logs->count() > 0)
        <div class="card" style="padding:0;overflow:hidden;border-radius:20px;border:1px solid var(--border);">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-light);border-bottom:1px solid var(--border);">
                        <th
                            style="padding:16px 24px;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;width:180px;">
                            Event Time</th>
                        <th
                            style="padding:16px 24px;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;width:200px;">
                            Principal</th>
                        <th
                            style="padding:16px 24px;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">
                            Action Context</th>
                        <th
                            style="padding:16px 24px;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;width:120px;">
                            Severity</th>
                        <th style="padding:16px 24px;width:60px;"></th>
                    </tr>
                </thead>
                <tbody style="font-size:13px;">
                    @foreach($logs as $log)
                        <tr x-data="{ open: false }" :class="open ? 'expanded' : ''" class="log-row">
                            {{-- Timestamp --}}
                            <td style="padding:16px 24px;white-space:nowrap;vertical-align:top;">
                                <div style="font-weight:700;color:var(--text-primary);">{{ $log->created_at->format('M d, H:i:s') }}
                                </div>
                                <div class="detail-ghost" style="margin-top:2px;">{{ $log->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- User / Principal --}}
                            <td style="padding:16px 24px;vertical-align:top;">
                                @if($log->user)
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div
                                            style="width:32px;height:32px;background:var(--accent-light);color:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:11px;">
                                            {{ substr($log->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('super-admin.users.index', ['search' => $log->user->email]) }}"
                                                class="resource-link">
                                                {{ $log->user->name }}
                                            </a>
                                            <div style="font-size:11px;color:var(--text-muted);">{{ $log->ip_address ?? 'Unknown IP' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div
                                            style="width:32px;height:32px;background:#f1f5f9;color:#64748b;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:700;color:var(--text-secondary);">SYSTEM</div>
                                            <div style="font-size:11px;color:var(--text-muted);">Internal Process</div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- Action Description --}}
                            <td style="padding:16px 24px;vertical-align:top;">
                                <div style="font-weight:600;color:var(--text-primary);margin-bottom:6px;line-height:1.4;">
                                    {{ $log->action }}
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                                    @if($log->model_type)
                                        @php
                                            $modelName = class_basename($log->model_type);
                                            $route = match ($modelName) {
                                                'Plan' => route('super-admin.plans.index'), // Link to grid for now
                                                'Organization' => route('super-admin.organizations.index'),
                                                'Service' => route('super-admin.services'),
                                                default => null
                                            };
                                        @endphp
                                        @if($route)
                                            <a href="{{ $route }}" class="badge-outline"
                                                style="text-decoration:none;display:flex;align-items:center;gap:6px;">
                                                <i class="fas fa-link" style="font-size:9px;opacity:0.5;"></i>
                                                {{ $modelName }}:{{ $log->model_id }}
                                            </a>
                                        @else
                                            <span class="badge-outline">{{ $modelName }}:{{ $log->model_id }}</span>
                                        @endif
                                    @endif

                                    @if($log->session_id)
                                        <span title="Session Tracking ID"
                                            style="font-family:monospace;font-size:10px;color:var(--text-muted);background:rgba(0,0,0,0.03);padding:2px 6px;border-radius:4px;">
                                            SID:{{ substr($log->session_id, 0, 8) }}...
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Severity --}}
                            <td style="padding:16px 24px;vertical-align:top;">
                                @php
                                    $style = match ($log->severity) {
                                        'danger' => ['bg' => 'rgba(220,38,38,0.1)', 'color' => 'var(--danger)', 'icon' => 'fa-bolt'],
                                        'warning' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => '#d97706', 'icon' => 'fa-triangle-exclamation'],
                                        default => ['bg' => 'rgba(37,99,235,0.1)', 'color' => 'var(--accent)', 'icon' => 'fa-info-circle']
                                    };
                                @endphp
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:8px;background:{{ $style['bg'] }};color:{{ $style['color'] }};font-size:10px;font-weight:800;text-transform:uppercase;">
                                    <i class="fas {{ $style['icon'] }}"></i>
                                    {{ $log->severity }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="padding:16px 24px;text-align:right;vertical-align:top;">
                                <button @click="open = !open" class="btn btn-secondary btn-icon"
                                    style="width:32px;height:32px;border-radius:8px;">
                                    <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size:10px;"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Expandable Detail Row --}}
                        <tr x-show="open" x-cloak style="background: #f8fafc;">
                            <td colspan="5" style="padding:0 24px 32px 24px;">
                                <div
                                    style="display:grid;grid-template-columns: 1fr 2fr;gap:32px;padding-top:16px;border-top:1px solid #e2e8f0;">
                                    {{-- Metadata Column --}}
                                    <div>
                                        <h5
                                            style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:16px;letter-spacing:0.05em;">
                                            Technical Details</h5>
                                        <div style="display:grid;gap:12px;">
                                            <div>
                                                <div class="detail-ghost">IP ADDRESS</div>
                                                <div style="font-weight:600;font-family:monospace;">
                                                    {{ $log->ip_address ?? 'Not Recorded' }}</div>
                                            </div>
                                            <div>
                                                <div class="detail-ghost">USER AGENT</div>
                                                <div
                                                    style="font-size:11px;color:var(--text-secondary);line-height:1.4;max-width:300px;">
                                                    {{ $log->user_agent ?? 'Direct System Access' }}</div>
                                            </div>
                                            <div>
                                                <div class="detail-ghost">BROWSER / OS</div>
                                                <div style="font-size:11px;font-weight:600;">
                                                    {{ Str::contains($log->user_agent, 'Windows') ? 'Windows' : 'Unix/Other' }} •
                                                    {{ Str::contains($log->user_agent, 'Chrome') ? 'Chrome' : 'Unknown Browser' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Payload Column --}}
                                    <div>
                                        <div
                                            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                                            <h5
                                                style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
                                                Trace Payload</h5>
                                            <button class="btn btn-secondary" style="font-size:10px;padding:4px 8px;"
                                                onclick="navigator.clipboard.writeText('{{ json_encode($log->payload) }}')">
                                                <i class="fas fa-copy" style="margin-right:4px;"></i> Copy JSON
                                            </button>
                                        </div>
                                        <pre
                                            class="payload-box"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($logs->hasPages())
                <div style="padding:24px;background:var(--bg-light);border-top:1px solid var(--border);">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @else
        <div style="text-align:center;padding:120px 40px;background:white;border-radius:32px;border:1px dashed var(--border);">
            <div
                style="width:80px;height:80px;background:var(--bg-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                <i class="fas fa-microchip" style="font-size:32px;color:var(--text-muted);opacity:0.2;"></i>
            </div>
            <h2 style="font-size:22px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;">No signals detected
            </h2>
            <p style="font-size:15px;color:var(--text-muted);max-width:400px;margin:12px auto 32px;line-height:1.6;">The audit
                vault is currently empty. Actions across the ecosystem will be logged here with cryptographic integrity.</p>
        </div>
    @endif

@endsection