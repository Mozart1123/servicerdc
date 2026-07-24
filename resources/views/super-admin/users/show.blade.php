@extends('layouts.super-admin')

@section('page_title', 'User Details')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">User Details</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Viewing profile for {{ $user->name }}</p>
        </div>
        <div>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary" style="margin-right: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
            <a href="{{ route('super-admin.users.edit', $user->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <div style="display: flex; align-items: center; margin-bottom: 24px;">
            <div class="user-avatar" style="width: 64px; height: 64px; font-size: 24px; margin-right: 16px;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h2 style="font-size: 18px; font-weight: 700; margin: 0;">{{ $user->name }}</h2>
                <p style="color: var(--text-muted); font-size: 14px; margin: 4px 0 0;">{{ $user->email }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">Role</div>
                <div style="font-size: 14px; font-weight: 500;">
                    @if($user->role === 'super_admin')
                        <span style="color: var(--blue);">Super Admin</span>
                    @elseif($user->role === 'admin')
                        <span style="color: var(--amber);">Admin</span>
                    @else
                        User
                    @endif
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">User Type</div>
                <div style="font-size: 14px; font-weight: 500;">{{ ucfirst($user->user_type) }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">Status</div>
                <div style="font-size: 14px; font-weight: 500;">
                    @if($user->status === 'active')
                        <span style="color: var(--green);"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Active</span>
                    @else
                        <span style="color: var(--red);"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Suspended</span>
                    @endif
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">Phone</div>
                <div style="font-size: 14px; font-weight: 500;">{{ $user->phone ?? 'Not provided' }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">Joined On</div>
                <div style="font-size: 14px; font-weight: 500;">{{ $user->created_at->format('M d, Y') }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;">Last Updated</div>
                <div style="font-size: 14px; font-weight: 500;">{{ $user->updated_at->format('M d, Y') }}</div>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 16px; display: flex; gap: 12px;">
            <form action="{{ route('super-admin.users.toggle-status', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    @if($user->status === 'active')
                        <i class="fas fa-ban" style="color: var(--red);"></i> Suspend User
                    @else
                        <i class="fas fa-check" style="color: var(--green);"></i> Activate User
                    @endif
                </button>
            </form>
            
            <form action="{{ route('super-admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary" style="color: var(--red); border-color: #fee2e2; background: #fef2f2;">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

@endsection
