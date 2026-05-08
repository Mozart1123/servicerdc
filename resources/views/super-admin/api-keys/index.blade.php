@extends('layouts.super-admin')

@section('page_title', 'API Key Management')

@section('content')

    {{-- ─── PAGE HEADER 🚀 ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;">API Key Management</h1>
            <p style="font-size:14px;color:var(--text-muted);margin-top:4px;">Manage secure integration credentials for the platform</p>
        </div>
        <button onclick="document.getElementById('newKeyModal').style.display='flex'" class="btn btn-primary" style="padding:10px 20px;border-radius:12px;font-weight:700;">
            <i class="fas fa-plus" style="margin-right:8px;"></i> Generate New Key
        </button>
    </div>

    {{-- ─── SENSITIVE DATA ALERT 🔒 ─── --}}
    @if(session('plain_token'))
        <div class="card" x-data="{ copied: false }" style="background:rgba(37,99,235,0.05);border:1px solid var(--accent);margin-bottom:32px;padding:24px;border-radius:20px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:var(--accent);color:white;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <h4 style="font-weight:800;color:var(--text-primary);font-size:16px;">New API Key Generated</h4>
                    <p style="font-size:13px;color:var(--text-secondary);">Please copy this key now. For security, it will NOT be shown again.</p>
                </div>
            </div>
            
            <div style="background:white;padding:16px;border-radius:12px;border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <code style="font-family:'JetBrains Mono','Fira Code',monospace;font-size:14px;font-weight:600;color:var(--accent);letter-spacing:0.5px;">{{ session('plain_token') }}</code>
                <button @click="navigator.clipboard.writeText('{{ session('plain_token') }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                    class="btn btn-secondary" style="font-size:12px;padding:8px 16px;">
                    <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'" style="margin-right:8px;"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy Key'"></span>
                </button>
            </div>
        </div>
    @endif

    {{-- ─── API KEYS LIST 🛡️ ─── --}}
    <div class="card" style="padding:0;overflow:hidden;border-radius:20px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--bg-light);border-bottom:1px solid var(--border);">
                    <th style="padding:16px 24px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">Key Name</th>
                    <th style="padding:16px 24px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">Identifier</th>
                    <th style="padding:16px 24px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">Last Used</th>
                    <th style="padding:16px 24px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">Status</th>
                    <th style="padding:16px 24px;text-align:right;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size:13px;">
                @forelse($keys as $key)
                    <tr style="border-bottom:1px solid var(--border-light);transition:all 0.2s;" onmouseover="this.style.background='var(--bg-light)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:16px 24px;">
                            <div style="font-weight:700;color:var(--text-primary);">{{ $key->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">Created {{ $key->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="padding:16px 24px;">
                            <code style="background:var(--bg-light);padding:4px 8px;border-radius:6px;font-family:monospace;font-size:12px;">•••• •••• {{ $key->token_last_four }}</code>
                        </td>
                        <td style="padding:16px 24px;">
                            <span style="color:var(--text-secondary);">
                                {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Never' }}
                            </span>
                        </td>
                        <td style="padding:16px 24px;">
                            @if($key->status === 'active')
                                <span style="background:rgba(34,197,94,0.1);color:#16a34a;padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">
                                    <i class="fas fa-circle-check" style="margin-right:4px;"></i> Active
                                </span>
                            @else
                                <span style="background:rgba(239,68,68,0.1);color:var(--danger);padding:4px 10px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;">
                                    <i class="fas fa-circle-xmark" style="margin-right:4px;"></i> Revoked
                                </span>
                            @endif
                        </td>
                        <td style="padding:16px 24px;text-align:right;">
                            @if($key->status === 'active')
                                <form action="{{ route('super-admin.system.api-keys.revoke', $key) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this key?')">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="color:var(--danger);padding:6px 14px;font-size:12px;border-radius:8px;">
                                        Revoke Key
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:64px;text-align:center;">
                            <div style="width:60px;height:60px;background:var(--bg-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <i class="fas fa-key" style="font-size:24px;color:var(--text-muted);opacity:0.3;"></i>
                            </div>
                            <h3 style="font-size:18px;font-weight:700;color:var(--text-primary);">No API keys found</h3>
                            <p style="color:var(--text-muted);font-size:14px;margin-top:8px;">Generate your first key to start integrating.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ─── NEW KEY MODAL 🧩 ─── --}}
    <div id="newKeyModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
        <div class="card" style="width:100%;max-width:480px;padding:32px;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <h2 style="font-size:20px;font-weight:800;letter-spacing:-0.5px;">Generate New API Key</h2>
                <button onclick="document.getElementById('newKeyModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:18px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('super-admin.system.api-keys.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;">Key Name</label>
                    <input type="text" name="name" required placeholder="e.g. Mobile App Dev, Production Server" 
                        style="width:100%;padding:12px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-light);outline:none;">
                </div>
                
                <div style="margin-bottom:32px;">
                    <label style="display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;">Expiration (Optional)</label>
                    <input type="date" name="expires_at" 
                        style="width:100%;padding:12px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-light);outline:none;">
                    <p style="font-size:11px;color:var(--text-muted);margin-top:6px;">Leave blank for a key that never expires.</p>
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="button" onclick="document.getElementById('newKeyModal').style.display='none'" class="btn btn-secondary" style="flex:1;padding:12px;border-radius:12px;font-weight:700;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;padding:12px;border-radius:12px;font-weight:700;">Generate Key</button>
                </div>
            </form>
        </div>
    </div>

@endsection
