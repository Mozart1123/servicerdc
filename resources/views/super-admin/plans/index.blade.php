@extends('layouts.super-admin')

@section('page_title', 'Plans & Features')

@section('content')

    {{-- ─── CSS OVERRIDES FOR PREMIUM LOOK ─── --}}
    <style>
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 32px;
            margin-top: 16px;
        }

        .plan-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            border-color: var(--accent-light);
        }

        .plan-card.featured {
            border: 2px solid var(--accent);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
        }

        .plan-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--accent);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 16px;
            border-bottom-left-radius: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .price-value {
            font-size: 40px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .price-period {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .feature-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
        }

        .inactive-card {
            opacity: 0.7;
            filter: grayscale(0.5);
        }

        /* Glassmorphism empty state */
        .empty-state-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px dashed var(--border);
            border-radius: 24px;
            padding: 80px 40px;
            text-align: center;
        }
    </style>

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:40px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;">Plans & Features</h1>
            <p style="font-size:14px;color:var(--text-muted);margin-top:4px;">Define pricing strategy and toggle
                capabilities across your platform</p>
        </div>
        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary"
            style="padding: 10px 20px; border-radius: 10px; font-weight: 600;">
            <i class="fas fa-plus" style="margin-right:8px;"></i> Create New Plan
        </a>
    </div>

    {{-- ─── PLANS VIEW ─── --}}
    @if($plans->count() > 0)
        <div class="pricing-grid">
            @foreach($plans as $plan)
                <div class="plan-card {{ $plan->is_featured ? 'featured' : '' }} {{ !$plan->is_active ? 'inactive-card' : '' }}">
                    @if($plan->is_featured)
                        <div class="plan-badge">Popular</div>
                    @endif

                    <div style="margin-bottom:24px;">
                        <div
                            style="font-size:12px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;">
                            {{ $plan->billing_period }} Tier
                        </div>
                        <h2 style="font-size:22px;font-weight:800;color:var(--text-primary);margin-bottom:8px;">{{ $plan->name }}
                        </h2>
                        <p style="font-size:14px;color:var(--text-muted);line-height:1.5;min-height:42px;">
                            {{ $plan->description ?? 'Empower your operations with our managed ' . strtolower($plan->name) . ' capabilities.' }}
                        </p>
                    </div>

                    <div style="margin-bottom:32px;display:flex;align-items:baseline;gap:4px;">
                        <span class="price-value">{{ number_format($plan->price, 2) }}</span>
                        <span class="price-period">/
                            {{ $plan->billing_period === 'monthly' ? 'mo' : ($plan->billing_period === 'yearly' ? 'yr' : 'once') }}</span>
                    </div>

                    <div style="margin-bottom:32px;flex-grow:1;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:16px;">Core features:
                        </div>
                        <div style="display:grid;gap:2px;">
                            @forelse($plan->features as $feature)
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-check"></i></div>
                                    <span style="font-weight:500;">{{ $feature->name }}</span>
                                    @if($feature->value)
                                        <span style="color:var(--text-muted);margin-left:auto;font-size:12px;">{{ $feature->value }}</span>
                                    @endif
                                </div>
                            @empty
                                <div
                                    style="padding:16px;background:var(--bg-light);border-radius:12px;text-align:center;font-size:13px;color:var(--text-muted);border:1px solid var(--border);">
                                    No specific features defined.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:12px;padding-top:24px;border-top:1px solid var(--border);">
                        <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-secondary"
                            style="flex:1;justify-content:center;border-radius:10px;">
                            <i class="fas fa-gear"></i> Manage
                        </a>

                        <div x-data="{ open: false }" style="position:relative;">
                            <button class="btn btn-secondary btn-icon" style="width:40px;height:40px;border-radius:10px;"
                                @click="open = !open">
                                <i class="fas fa-ellipsis"></i>
                            </button>
                            <div class="action-dropdown" x-show="open" x-cloak @click.outside="open = false"
                                style="right:0;bottom:100%;top:auto;margin-bottom:12px;min-width:180px;box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);">
                                <form method="POST" action="{{ route('super-admin.plans.toggle-status', $plan) }}">
                                    @csrf
                                    <button type="submit" class="action-item"
                                        style="width:100%;background:none;border:none;text-align:left;font-family:inherit;padding:10px 16px;">
                                        <i class="fas {{ $plan->is_active ? 'fa-eye-slash' : 'fa-eye' }}"
                                            style="margin-right:10px;width:16px;"></i>
                                        {{ $plan->is_active ? 'Suspend Tier' : 'Restore Tier' }}
                                    </button>
                                </form>
                                <div style="height:1px;background:var(--border);margin:6px 0;"></div>
                                <form method="POST" action="{{ route('super-admin.plans.destroy', $plan) }}"
                                    onsubmit="return confirm('Immediately delete {{ addslashes($plan->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-item danger"
                                        style="width:100%;background:none;border:none;text-align:left;font-family:inherit;padding:10px 16px;">
                                        <i class="fas fa-trash" style="margin-right:10px;width:16px;"></i>
                                        Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state-glass">
            <div
                style="width:80px;height:80px;background:rgba(37,99,235,0.05);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                <i class="fas fa-layer-group" style="font-size:32px;color:var(--accent);"></i>
            </div>
            <h2 style="font-size:22px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;">Tier Architecture Empty
            </h2>
            <p style="font-size:15px;color:var(--text-muted);max-width:400px;margin:12px auto 32px;line-height:1.6;">Build your
                platform's economic foundation by creating your first subscription plan.</p>
            <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary"
                style="padding: 12px 32px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2);">
                Initialize First Plan
            </a>
        </div>
    @endif

@endsection