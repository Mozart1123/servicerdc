@extends('layouts.super-admin')

@section('page_title', 'Edit User')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Edit User</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Update information for {{ $user->name }}</p>
        </div>
        <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card">
        <form action="{{ route('super-admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label for="name" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="filter-input" style="width:100%;max-width:400px;" required>
                @error('name') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label for="email" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="filter-input" style="width:100%;max-width:400px;" required>
                @error('email') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label for="password" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">Password (Leave blank to keep current)</label>
                <input type="password" id="password" name="password" class="filter-input" style="width:100%;max-width:400px;">
                @error('password') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label for="phone" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">Phone (optional)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="filter-input" style="width:100%;max-width:400px;">
                @error('phone') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label for="role" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">Role</label>
                <select id="role" name="role" class="filter-select" style="width:100%;max-width:400px;" required>
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="user_type" style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">User Type</label>
                <select id="user_type" name="user_type" class="filter-select" style="width:100%;max-width:400px;" required>
                    <option value="client" {{ old('user_type', $user->user_type) === 'client' ? 'selected' : '' }}>Client</option>
                    <option value="artisan" {{ old('user_type', $user->user_type) === 'artisan' ? 'selected' : '' }}>Artisan</option>
                    <option value="job_seeker" {{ old('user_type', $user->user_type) === 'job_seeker' ? 'selected' : '' }}>Job Seeker</option>
                    <option value="recruiter" {{ old('user_type', $user->user_type) === 'recruiter' ? 'selected' : '' }}>Recruiter</option>
                </select>
                @error('user_type') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>

@endsection
