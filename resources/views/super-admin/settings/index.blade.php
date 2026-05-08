@extends('layouts.super-admin')

@section('page_title', 'System Settings')

@section('content')

    <div x-data="{ tab: 'general' }">
        {{-- ─── TAB NAVIGATION ─── --}}
        <div class="card" style="margin-bottom:24px;padding:0;">
            <div style="display:flex;padding:0 12px;border-bottom:1px solid var(--border);">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'tab-active' : ''" class="tab-btn">
                    <i class="fas fa-sliders"></i> General
                </button>
                <button @click="tab = 'contact'" :class="tab === 'contact' ? 'tab-active' : ''" class="tab-btn">
                    <i class="fas fa-envelope"></i> Contact & Support
                </button>
                <button @click="tab = 'security'" :class="tab === 'security' ? 'tab-active' : ''" class="tab-btn">
                    <i class="fas fa-shield-halved"></i> Security & Maintenance
                </button>
                <button @click="tab = 'appearance'" :class="tab === 'appearance' ? 'tab-active' : ''" class="tab-btn">
                    <i class="fas fa-palette"></i> Appearance
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('super-admin.system.settings.update') }}">
            @csrf

            {{-- ─── GENERAL TAB ─── --}}
            <div x-show="tab === 'general'" x-transition>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">General Platform Settings</h3>
                    </div>
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                            <div class="form-group">
                                <label class="form-label">Application Name</label>
                                <input type="text" name="app_name" value="{{ $settings['app_name'] ?? '' }}"
                                    class="form-input" placeholder="e.g. ServiceRDC">
                                <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">The name used throughout
                                    the platform and emails.</p>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Default Currency</label>
                                <select name="default_currency" class="form-select">
                                    <option value="USD" {{ ($settings['default_currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ ($settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="CDF" {{ ($settings['default_currency'] ?? '') == 'CDF' ? 'selected' : '' }}>CDF - Congolese Franc</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:16px;">
                            <label class="form-label" style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                                <input type="checkbox" name="allow_registration" value="1" {{ ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' }}
                                    style="width:16px;height:16px;accent-color:var(--accent);">
                                Allow Public Registration
                            </label>
                            <p style="font-size:11px;color:var(--text-muted);margin-left:26px;">If disabled, new users can
                                only be invited by an admin.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── CONTACT TAB ─── --}}
            <div x-show="tab === 'contact'" x-transition x-cloak>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Support & Contact Information</h3>
                    </div>
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                            <div class="form-group">
                                <label class="form-label">Support Email Address</label>
                                <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}"
                                    class="form-input" placeholder="support@example.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Support Phone Number</label>
                                <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '' }}"
                                    class="form-input" placeholder="+243 ...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── SECURITY TAB ─── --}}
            <div x-show="tab === 'security'" x-transition x-cloak>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Security & System Maintenance</h3>
                    </div>
                    <div class="card-body">
                        <div
                            style="background:var(--bg-main);padding:20px;border-radius:var(--radius);border:1px solid var(--border);display:flex;align-items:center;gap:20px;">
                            <div
                                style="width:48px;height:48px;background:#fee2e2;color:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:15px;color:var(--text-primary);">Maintenance Mode
                                </div>
                                <div style="font-size:13px;color:var(--text-secondary);">When active, only Super Admins can
                                    access the platform. Everyone else will see a maintenance page.</div>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" id="maintenance_toggle" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label for="maintenance_toggle" class="toggle-slider"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── APPEARANCE TAB ─── --}}
            <div x-show="tab === 'appearance'" x-transition x-cloak>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Appearance & Branding</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Footer Copyright Text</label>
                            <input type="text" name="footer_text" value="{{ $settings['footer_text'] ?? '' }}"
                                class="form-input">
                        </div>
                        <div class="form-group" style="margin-top:20px;">
                            <label class="form-label">System Branding Color (HEX)</label>
                            <div style="display:flex;gap:12px;align-items:center;">
                                <input type="color" value="#2563eb" disabled
                                    style="width:40px;height:40px;padding:2px;border:1px solid var(--border);border-radius:4px;cursor:not-allowed;">
                                <input type="text" value="#2563eb" readonly class="form-input"
                                    style="background:var(--bg-main);width:120px;font-family:monospace;">
                                <span style="font-size:12px;color:var(--text-muted);"><i class="fas fa-lock"></i> Fixed by
                                    Design System</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── FORM FOOTER ─── --}}
            <div style="display:flex;justify-content:flex-end;margin-top:24px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <style>
        .tab-btn {
            padding: 14px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: var(--text-primary);
            background: rgba(0, 0, 0, .02);
        }

        .tab-active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

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

        .form-select {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--text-primary);
            outline: none;
        }

        /* ─── TOGGLE SWITCH ─── */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        }

        input:checked+.toggle-slider {
            background-color: var(--accent);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }
    </style>

@endsection