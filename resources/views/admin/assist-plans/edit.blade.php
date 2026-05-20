@extends('layouts.admin')

@section('title', 'Edit '.$plan->name)
@section('page_title', 'Edit plan: '.$plan->name)

@section('content')
<div class="assist-admin-card glass-panel mb-4">
    <p class="assist-text-muted mb-0">Slug: <code>{{ $plan->slug }}</code> — used in checkout URLs and the desktop app.</p>
</div>

<form method="POST" action="{{ route('admin.assist.plans.update', $plan) }}">
    @csrf
    @method('PUT')

    <div class="assist-admin-card glass-panel mb-6">
        <h2>Plan details</h2>
        <div class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 720px;">
            <x-assist.input label="Display name" name="name" :value="old('name', $plan->name)" required />
            <x-assist.input label="Sort order" name="sort_order" type="number" :value="old('sort_order', $plan->sort_order)" required />
            <div class="assist-field" style="grid-column: 1 / -1;">
                <label for="description">Description (pricing card)</label>
                <textarea id="description" name="description" class="assist-input" rows="3">{{ old('description', $plan->description) }}</textarea>
            </div>
            @if ($plan->slug === 'free')
                <div class="assist-field">
                    <label>Price NGN</label>
                    <input type="number" class="assist-input" value="0" readonly>
                    <input type="hidden" name="price_ngn" value="0">
                </div>
                <div class="assist-field">
                    <label>Price USD</label>
                    <input type="number" class="assist-input" value="0" readonly>
                    <input type="hidden" name="price_usd" value="0">
                </div>
            @else
                <x-assist.input label="Price NGN (whole naira)" name="price_ngn" type="number" :value="old('price_ngn', $plan->price_ngn)" required />
                <x-assist.input label="Price USD" name="price_usd" type="number" step="0.01" :value="old('price_usd', $plan->price_usd)" required />
            @endif
            <div class="assist-field">
                <label for="usage_period">Usage period</label>
                <select id="usage_period" name="usage_period" class="assist-input" required>
                    <option value="weekly" @selected(old('usage_period', $plan->usage_period) === 'weekly')>Weekly limits</option>
                    <option value="monthly" @selected(old('usage_period', $plan->usage_period) === 'monthly')>Monthly limits</option>
                </select>
            </div>
            <div class="assist-field">
                <label for="is_active">Visible on pricing page</label>
                <select id="is_active" name="is_active" class="assist-input" required>
                    <option value="1" @selected(old('is_active', $plan->is_active ? '1' : '0') === '1')>Active</option>
                    <option value="0" @selected(old('is_active', $plan->is_active ? '1' : '0') === '0')>Hidden</option>
                </select>
            </div>
            <div class="assist-field">
                <label for="is_featured">Featured (“Most popular” badge)</label>
                <select id="is_featured" name="is_featured" class="assist-input" required>
                    <option value="0" @selected(old('is_featured', $plan->is_featured ? '1' : '0') === '0')>No</option>
                    <option value="1" @selected(old('is_featured', $plan->is_featured ? '1' : '0') === '1')>Yes</option>
                </select>
            </div>
        </div>
    </div>

    <div class="assist-admin-card glass-panel mb-6">
        <h2>Feature limits</h2>
        <p class="assist-text-muted" style="margin-bottom: 16px;">Leave blank to omit from marketing list. Enter a number for capped usage, or <code>unlimited</code> for no cap.</p>
        <div class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 720px;">
            @php $limits = old('limits', $plan->limits ?? []); @endphp
            @foreach ($features as $feature)
                @php
                    $val = $limits[$feature] ?? '';
                    if ($val === null) {
                        $val = 'unlimited';
                    }
                @endphp
                <x-assist.input
                    :label="$featureLabels[$feature] ?? $feature"
                    :name="'limits['.$feature.']'"
                    :value="$val"
                />
            @endforeach
        </div>
    </div>

    <button type="submit" class="assist-btn assist-btn-primary">Save plan</button>
    <a href="{{ route('admin.assist.plans') }}" class="assist-btn assist-btn-ghost" style="margin-left: 8px;">Back</a>
</form>
@endsection
