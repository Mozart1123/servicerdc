@extends('layouts.super-admin')

@section('page_title', 'Create Organization')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <a href="{{ route('super-admin.organizations.index') }}"
                style="color:var(--text-muted);text-decoration:none;font-size:13px;">
                <i class="fas fa-arrow-left"></i> Back to Organizations
            </a>
        </div>
        <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Create New Organization
        </h1>
        <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Add a new entity or company to the platform</p>
    </div>

    {{-- ─── CREATE FORM ─── --}}
    <div class="card" style="max-width:800px;">
        <form action="{{ route('super-admin.organizations.store') }}" method="POST" style="padding:24px;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
                {{-- Name --}}
                <div style="grid-column: span 2;">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Organization
                        Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="filter-input" style="width:100%;"
                        placeholder="e.g. Acme Corp" required>
                    @error('name') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Organization
                        Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="filter-input" style="width:100%;"
                        placeholder="contact@acme.com">
                    @error('email') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Phone
                        Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="filter-input" style="width:100%;"
                        placeholder="+243 ...">
                    @error('phone') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Website --}}
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Website
                        URL</label>
                    <input type="url" name="website" value="{{ old('website') }}" class="filter-input" style="width:100%;"
                        placeholder="https://acme.com">
                    @error('website') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Owner --}}
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Owner
                        User</label>
                    <select name="owner_id" class="filter-select" style="width:100%;">
                        <option value="">Select an owner (Optional)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Initial
                        Status</label>
                    <select name="status" class="filter-select" style="width:100%;">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Address --}}
                <div style="grid-column: span 2;">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Office
                        Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="filter-input" style="width:100%;"
                        placeholder="123 Street, City, Country">
                    @error('address') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div style="grid-column: span 2;">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Description</label>
                    <textarea name="description" class="filter-input" style="width:100%;min-height:100px;padding-top:10px;"
                        placeholder="Brief overview of the organization...">{{ old('description') }}</textarea>
                    @error('description') <p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div
                style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding-top:20px;border-top:1px solid var(--border);">
                <a href="{{ route('super-admin.organizations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Organization</button>
            </div>
        </form>
    </div>

@endsection