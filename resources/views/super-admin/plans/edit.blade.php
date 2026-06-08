@extends('layouts.super-admin')

@section('page_title', 'Edit Plan: ' . $plan->name)

@section('content')

    <div style="max-width:800px;margin:0 auto;">
        <div style="margin-bottom:24px;">
            <a href="{{ route('super-admin.plans.index') }}"
                style="display:inline-flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Back to Plans
            </a>
            <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);margin-top:12px;">Edit Plan:
                {{ $plan->name }}</h1>
        </div>

        <form method="POST" action="{{ route('super-admin.plans.update', $plan) }}" x-data="{ 
            features: {{ $plan->features->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'value' => $f->value])->toJson() }},
            addFeature() { this.features.push({ name: '', value: '' }) },
            removeFeature(index) { this.features.splice(index, 1) }
        }">
            @csrf
            @method('PUT')

            <div class="card" style="margin-bottom:24px;">
                <div style="padding:8px 0;">
                    <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:20px;">Basic
                        Information</h3>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="form-input"
                                placeholder="e.g. Professional" required>
                        </div>
                        <div>
                            <label class="form-label">Price</label>
                            <div style="position:relative;">
                                <span
                                    style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;">$</span>
                                <input type="number" name="price" value="{{ old('price', $plan->price) }}" step="0.01"
                                    class="form-input" style="padding-left:24px;" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input" rows="3"
                            placeholder="Briefly describe what this plan is for...">{{ old('description', $plan->description) }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <label class="form-label">Billing Period</label>
                            <select name="billing_period" class="form-input">
                                <option value="monthly" {{ $plan->billing_period === 'monthly' ? 'selected' : '' }}>Monthly
                                </option>
                                <option value="yearly" {{ $plan->billing_period === 'yearly' ? 'selected' : '' }}>Yearly
                                </option>
                                <option value="lifetime" {{ $plan->billing_period === 'lifetime' ? 'selected' : '' }}>Lifetime
                                </option>
                            </select>
                        </div>
                        <div style="display:flex;align-items:flex-end;padding-bottom:10px;">
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                                <input type="checkbox" name="is_featured" value="1" {{ $plan->is_featured ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent);">
                                <span style="font-size:13px;font-weight:500;color:var(--text-primary);">Featured Plan</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:24px;">
                <div style="padding:8px 0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                        <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);">Plan Features</h3>
                        <button type="button" class="btn btn-secondary btn-sm" @click="addFeature()">
                            <i class="fas fa-plus"></i> Add Feature
                        </button>
                    </div>

                    <div style="display:grid;gap:12px;">
                        <template x-for="(feature, index) in features" :key="index">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <input type="hidden" :name="`features[${index}][id]`" x-model="feature.id">
                                <div style="flex:1;">
                                    <input type="text" :name="`features[${index}][name]`" x-model="feature.name"
                                        class="form-input" placeholder="Feature Name" required>
                                </div>
                                <div style="flex:1;">
                                    <input type="text" :name="`features[${index}][value]`" x-model="feature.value"
                                        class="form-input" placeholder="Value (optional)">
                                </div>
                                <button type="button" class="btn btn-secondary btn-icon" @click="removeFeature(index)"
                                    :disabled="features.length === 1">
                                    <i class="fas fa-trash" style="font-size:11px;"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Update Plan</button>
                <a href="{{ route('super-admin.plans.index') }}" class="btn btn-secondary"
                    style="flex:1;text-align:center;">Cancel Changes</a>
            </div>
        </form>
    </div>

@endsection