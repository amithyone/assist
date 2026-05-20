@extends('layouts.admin')

@section('title', 'Vouchers')
@section('page_title', 'Vouchers & discounts')

@section('content')
<div class="assist-admin-card glass-panel mb-6">
    <h2>Create voucher</h2>
    <p class="assist-text-muted" style="margin-bottom: 16px;">Customers enter the code on the pricing page at checkout. Codes are stored uppercase.</p>
    <form method="POST" action="{{ route('admin.assist.vouchers.store') }}" class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 800px;">
        @csrf
        <x-assist.input label="Code" name="code" :value="old('code')" required placeholder="LAUNCH20" />
        <x-assist.input label="Label (internal)" name="label" :value="old('label')" placeholder="Launch promo" />
        <div class="assist-field">
            <label for="discount_type">Discount type</label>
            <select id="discount_type" name="discount_type" class="assist-input" required>
                @foreach ($discountTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('discount_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <x-assist.input label="Discount value" name="discount_value" type="number" step="0.01" :value="old('discount_value')" required />
        <div class="assist-field">
            <label for="plan_slug">Applies to plan</label>
            <select id="plan_slug" name="plan_slug" class="assist-input">
                <option value="">All paid plans</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->slug }}" @selected(old('plan_slug') === $plan->slug)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <x-assist.input label="Max redemptions (optional)" name="max_redemptions" type="number" :value="old('max_redemptions')" />
        <x-assist.input label="Starts at (optional)" name="starts_at" type="datetime-local" :value="old('starts_at')" />
        <x-assist.input label="Expires at (optional)" name="expires_at" type="datetime-local" :value="old('expires_at')" />
        <div class="assist-field">
            <label for="is_active">Active</label>
            <select id="is_active" name="is_active" class="assist-input" required>
                <option value="1" @selected(old('is_active', '1') === '1')>Yes</option>
                <option value="0" @selected(old('is_active') === '0')>No</option>
            </select>
        </div>
        <div style="grid-column: 1 / -1;">
            <button type="submit" class="assist-btn assist-btn-primary">Create voucher</button>
        </div>
    </form>
</div>

<div class="assist-admin-card glass-panel">
    <h2>Existing vouchers</h2>
    <div class="assist-admin-table-wrap">
        <table class="assist-admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Plan</th>
                    <th>Used</th>
                    <th>Valid</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vouchers as $voucher)
                    <tr>
                        <td colspan="7" style="padding: 0; border: none;">
                            <details class="assist-voucher-row">
                                <summary style="cursor: pointer; padding: 12px 16px; list-style: none; display: grid; grid-template-columns: 1.2fr 1fr 0.8fr 0.7fr 1.2fr 0.6fr 80px; gap: 8px; align-items: center;">
                                    <span><strong>{{ $voucher->code }}</strong></span>
                                    <span>{{ $voucher->discountLabel() }}</span>
                                    <span>{{ $voucher->plan_slug ?? 'All' }}</span>
                                    <span>{{ $voucher->redemption_count }}@if($voucher->max_redemptions)/{{ $voucher->max_redemptions }}@endif</span>
                                    <span class="assist-text-muted" style="font-size: 12px;">
                                        @if ($voucher->starts_at) from {{ $voucher->starts_at->format('Y-m-d') }} @endif
                                        @if ($voucher->expires_at) until {{ $voucher->expires_at->format('Y-m-d') }} @endif
                                        @if (! $voucher->starts_at && ! $voucher->expires_at) Always @endif
                                    </span>
                                    <span>
                                        @if ($voucher->is_active)
                                            <span class="assist-admin-badge assist-admin-badge-success">On</span>
                                        @else
                                            <span class="assist-admin-badge assist-admin-badge-muted">Off</span>
                                        @endif
                                    </span>
                                    <span class="assist-text-muted">Edit</span>
                                </summary>
                                <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.06);">
                                    <form method="POST" action="{{ route('admin.assist.vouchers.update', $voucher) }}" class="assist-admin-form-grid assist-admin-form-grid-2" style="max-width: 800px;">
                                        @csrf
                                        @method('PUT')
                                        <x-assist.input label="Code" name="code" :value="old('code', $voucher->code)" required />
                                        <x-assist.input label="Label" name="label" :value="old('label', $voucher->label)" />
                                        <div class="assist-field">
                                            <label>Discount type</label>
                                            <select name="discount_type" class="assist-input" required>
                                                @foreach ($discountTypes as $value => $label)
                                                    <option value="{{ $value }}" @selected(old('discount_type', $voucher->discount_type) === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <x-assist.input label="Discount value" name="discount_value" type="number" step="0.01" :value="old('discount_value', $voucher->discount_value)" required />
                                        <div class="assist-field">
                                            <label>Plan</label>
                                            <select name="plan_slug" class="assist-input">
                                                <option value="">All paid plans</option>
                                                @foreach ($plans as $plan)
                                                    <option value="{{ $plan->slug }}" @selected(old('plan_slug', $voucher->plan_slug) === $plan->slug)>{{ $plan->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <x-assist.input label="Max redemptions" name="max_redemptions" type="number" :value="old('max_redemptions', $voucher->max_redemptions)" />
                                        <x-assist.input label="Starts at" name="starts_at" type="datetime-local" :value="old('starts_at', $voucher->starts_at?->format('Y-m-d\TH:i'))" />
                                        <x-assist.input label="Expires at" name="expires_at" type="datetime-local" :value="old('expires_at', $voucher->expires_at?->format('Y-m-d\TH:i'))" />
                                        <div class="assist-field">
                                            <label>Active</label>
                                            <select name="is_active" class="assist-input" required>
                                                <option value="1" @selected(old('is_active', $voucher->is_active ? '1' : '0') === '1')>Yes</option>
                                                <option value="0" @selected(old('is_active', $voucher->is_active ? '1' : '0') === '0')>No</option>
                                            </select>
                                        </div>
                                        <div style="grid-column: 1 / -1; display: flex; gap: 8px; flex-wrap: wrap;">
                                            <button type="submit" class="assist-btn assist-btn-primary assist-btn-sm">Save</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('admin.assist.vouchers.destroy', $voucher) }}" style="margin-top: 8px;" onsubmit="return confirm('Delete voucher {{ $voucher->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="assist-btn assist-btn-ghost assist-btn-sm" style="color: #f87171;">Delete</button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="assist-text-muted">No vouchers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
