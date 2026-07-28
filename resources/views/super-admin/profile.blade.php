@extends('layouts.super-admin')

@section('page_title', 'Mon Profil')

@section('content')

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;">Mon Profil</h1>
            <p style="font-size:13px;color:var(--text-muted);margin-top:2px;">Gérez vos informations personnelles et de sécurité</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <div class="card-header" style="margin-bottom: 20px;">
            <h3 class="card-title" style="font-size:16px;font-weight:600;margin:0;"><i class="fas fa-user-gear"></i> Informations du profil</h3>
        </div>
        
        <form action="{{ route('super-admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom: 24px;">
                <div class="form-group">
                    <label for="name" class="form-label" style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Nom complet</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" style="width:100%;" required>
                    @error('name') <span style="color:red;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label" style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Adresse Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" style="width:100%;" required>
                    @error('email') <span style="color:red;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border); margin: 24px 0;">

            <div class="card-header" style="margin-bottom: 20px;">
                <h3 class="card-title" style="font-size:16px;font-weight:600;margin:0;"><i class="fas fa-shield-halved"></i> Modifier le mot de passe</h3>
                <p style="font-size:12px;color:var(--text-muted);margin-top:4px;">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr;gap:16px;margin-bottom: 24px;max-width: 500px;">
                <div class="form-group">
                    <label for="current_password" class="form-label" style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" style="width:100%;">
                    @error('current_password') <span style="color:red;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label" style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input" style="width:100%;">
                    @error('password') <span style="color:red;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label" style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Confirmation du nouveau mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" style="width:100%;">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <style>
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--text-primary);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }
    </style>

@endsection
