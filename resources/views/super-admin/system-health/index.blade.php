@extends('layouts.super-admin')

@section('page_title', 'System Health')

@section('content')

    @php
        function formatHealthBytes($bytes)
        {
            if ($bytes <= 0)
                return '0 B';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = floor(log(max($bytes, 1), 1024));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }
    @endphp

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;">System Health</h1>
            <p style="font-size:14px;color:var(--text-muted);margin-top:4px;">Real-time platform diagnostics and
                infrastructure monitoring</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span
                style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse-dot 2s infinite;"></span>
            <span style="font-size:13px;font-weight:700;color:#16a34a;">All Systems Operational</span>
        </div>
    </div>

    {{-- ─── STATUS OVERVIEW CARDS ─── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px;">
        {{-- Server Uptime --}}
        <div class="card" style="padding:24px;border-radius:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:40px;height:40px;background:rgba(34,197,94,0.1);color:#16a34a;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-clock" style="font-size:18px;"></i>
                </div>
                <span
                    style="background:rgba(34,197,94,0.1);color:#16a34a;padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">Online</span>
            </div>
            <div style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-0.5px;">
                {{ $health['server']['uptime'] }}
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Server Uptime</div>
        </div>

        {{-- PHP Version --}}
        <div class="card" style="padding:24px;border-radius:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:40px;height:40px;background:rgba(99,102,241,0.1);color:#6366f1;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fab fa-php" style="font-size:20px;"></i>
                </div>
                <span
                    style="background:rgba(99,102,241,0.1);color:#6366f1;padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">Runtime</span>
            </div>
            <div style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-0.5px;">PHP
                {{ $health['php']['version'] }}
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">OPcache:
                {{ $health['php']['opcache_enabled'] ? 'Enabled' : 'Disabled' }}
            </div>
        </div>

        {{-- Database --}}
        <div class="card" style="padding:24px;border-radius:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:40px;height:40px;background:rgba(37,99,235,0.1);color:var(--accent);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-database" style="font-size:18px;"></i>
                </div>
                <span
                    style="background:rgba(37,99,235,0.1);color:var(--accent);padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">{{ $health['database']['status'] }}</span>
            </div>
            <div style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-0.5px;">
                {{ $health['database']['tables'] }} Tables
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">
                {{ strtoupper($health['database']['driver']) }} · {{ $health['database']['name'] }}
            </div>
        </div>

        {{-- Disk Usage --}}
        <div class="card" style="padding:24px;border-radius:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:40px;height:40px;background:rgba(245,158,11,0.1);color:#f59e0b;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-hard-drive" style="font-size:18px;"></i>
                </div>
                <span
                    style="background:{{ $health['storage']['disk_percent'] > 85 ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)' }};color:{{ $health['storage']['disk_percent'] > 85 ? 'var(--danger)' : '#f59e0b' }};padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">{{ $health['storage']['disk_percent'] }}%</span>
            </div>
            <div style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-0.5px;">
                {{ formatHealthBytes($health['storage']['disk_used']) }}
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">of
                {{ formatHealthBytes($health['storage']['disk_total']) }} total
            </div>
            {{-- Progress Bar --}}
            <div style="margin-top:12px;background:var(--bg-light);border-radius:6px;height:6px;overflow:hidden;">
                <div
                    style="height:100%;width:{{ $health['storage']['disk_percent'] }}%;background:{{ $health['storage']['disk_percent'] > 85 ? 'var(--danger)' : ($health['storage']['disk_percent'] > 60 ? '#f59e0b' : '#22c55e') }};border-radius:6px;transition:width 1s ease;">
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MAIN GRID ─── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">

        {{-- Server Info --}}
        <div class="card" style="padding:0;border-radius:20px;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;">
                <i class="fas fa-server" style="color:var(--accent);"></i>
                <h3 style="font-size:15px;font-weight:800;color:var(--text-primary);">Server Environment</h3>
            </div>
            <div style="padding:0;">
                @php
                    $serverRows = [
                        ['Hostname', $health['server']['hostname']],
                        ['Operating System', $health['server']['os']],
                        ['Server Software', $health['server']['server_software']],
                        ['Load Average (1m / 5m / 15m)', implode(' / ', $health['server']['load_avg'])],
                        ['Server Uptime', $health['server']['uptime']],
                    ];
                @endphp
                @foreach($serverRows as $row)
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;padding:14px 24px;border-bottom:1px solid var(--border-light);font-size:13px;">
                        <span style="color:var(--text-secondary);font-weight:600;">{{ $row[0] }}</span>
                        <code
                            style="background:var(--bg-light);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;color:var(--text-primary);">{{ $row[1] }}</code>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PHP Config --}}
        <div class="card" style="padding:0;border-radius:20px;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;">
                <i class="fab fa-php" style="color:#6366f1;font-size:18px;"></i>
                <h3 style="font-size:15px;font-weight:800;color:var(--text-primary);">PHP Configuration</h3>
            </div>
            <div style="padding:0;">
                @php
                    $phpRows = [
                        ['PHP Version', $health['php']['version']],
                        ['Memory Limit', $health['php']['memory_limit']],
                        ['Current Memory Usage', formatHealthBytes($health['php']['memory_usage'])],
                        ['Peak Memory Usage', formatHealthBytes($health['php']['memory_peak'])],
                        ['Max Execution Time', $health['php']['max_execution'] . 's'],
                        ['Upload Max Filesize', $health['php']['upload_max']],
                        ['Post Max Size', $health['php']['post_max']],
                        ['OPcache', $health['php']['opcache_enabled'] ? '✅ Enabled' : '❌ Disabled'],
                    ];
                @endphp
                @foreach($phpRows as $row)
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;padding:14px 24px;border-bottom:1px solid var(--border-light);font-size:13px;">
                        <span style="color:var(--text-secondary);font-weight:600;">{{ $row[0] }}</span>
                        <code
                            style="background:var(--bg-light);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;color:var(--text-primary);">{{ $row[1] }}</code>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── APPLICATION & DATABASE ─── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">

        {{-- Application --}}
        <div class="card" style="padding:0;border-radius:20px;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;">
                <i class="fas fa-cube" style="color:#8b5cf6;"></i>
                <h3 style="font-size:15px;font-weight:800;color:var(--text-primary);">Application Stack</h3>
            </div>
            <div style="padding:0;">
                @php
                    $appRows = [
                        ['App Name', $health['app']['name']],
                        ['Environment', $health['app']['env']],
                        ['Debug Mode', $health['app']['debug'] ? '⚠️ ON' : '✅ OFF'],
                        ['Laravel Version', $health['app']['laravel']],
                        ['Timezone', $health['app']['timezone']],
                        ['Cache Driver', $health['app']['cache_driver']],
                        ['Session Driver', $health['app']['session_driver']],
                        ['Queue Driver', $health['app']['queue_driver']],
                    ];
                @endphp
                @foreach($appRows as $row)
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;padding:14px 24px;border-bottom:1px solid var(--border-light);font-size:13px;">
                        <span style="color:var(--text-secondary);font-weight:600;">{{ $row[0] }}</span>
                        <code
                            style="background:var(--bg-light);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;color:var(--text-primary);">{{ $row[1] }}</code>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Database --}}
        <div class="card" style="padding:0;border-radius:20px;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;">
                <i class="fas fa-database" style="color:var(--accent);"></i>
                <h3 style="font-size:15px;font-weight:800;color:var(--text-primary);">Database Health</h3>
            </div>
            <div style="padding:0;">
                @php
                    $dbRows = [
                        ['Driver', strtoupper($health['database']['driver'])],
                        ['Connection Status', $health['database']['status']],
                        ['Database Name', $health['database']['name']],
                        ['Total Tables', $health['database']['tables']],
                        ['Database Size', formatHealthBytes($health['database']['size'])],
                    ];
                @endphp
                @foreach($dbRows as $row)
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;padding:14px 24px;border-bottom:1px solid var(--border-light);font-size:13px;">
                        <span style="color:var(--text-secondary);font-weight:600;">{{ $row[0] }}</span>
                        <code
                            style="background:var(--bg-light);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;color:var(--text-primary);">{{ $row[1] }}</code>
                    </div>
                @endforeach

                {{-- Quick Stats --}}
                <div style="padding:20px 24px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                    <div style="text-align:center;padding:12px;background:var(--bg-light);border-radius:12px;">
                        <div style="font-size:20px;font-weight:800;color:var(--accent);">{{ $health['app']['total_users'] }}
                        </div>
                        <div
                            style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-top:4px;">
                            Users</div>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--bg-light);border-radius:12px;">
                        <div style="font-size:20px;font-weight:800;color:#8b5cf6;">{{ $health['app']['total_services'] }}
                        </div>
                        <div
                            style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-top:4px;">
                            Services</div>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--bg-light);border-radius:12px;">
                        <div style="font-size:20px;font-weight:800;color:#f59e0b;">{{ $health['app']['recent_logs'] }}</div>
                        <div
                            style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-top:4px;">
                            Logs (24h)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── LOADED PHP EXTENSIONS ─── --}}
    <div class="card" style="padding:0;border-radius:20px;overflow:hidden;">
        <div
            style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
                <i class="fas fa-puzzle-piece" style="color:#ec4899;"></i>
                <h3 style="font-size:15px;font-weight:800;color:var(--text-primary);">Loaded PHP Extensions</h3>
            </div>
            <span
                style="background:var(--bg-light);padding:4px 12px;border-radius:8px;font-size:12px;font-weight:700;color:var(--text-secondary);">{{ count($health['php']['extensions']) }}
                loaded</span>
        </div>
        <div style="padding:20px 24px;display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($health['php']['extensions'] as $ext)
                <span
                    style="background:var(--bg-light);padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;color:var(--text-secondary);border:1px solid var(--border-light);">{{ $ext }}</span>
            @endforeach
        </div>
    </div>

    {{-- ─── PULSE DOT ANIMATION ─── --}}
    <style>
        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }
    </style>



@endsection