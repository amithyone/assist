@php
    $payment = $payment ?? [];
    $checkout = $payment['checkout'] ?? [];
    $paystack = $payment['paystack'] ?? [];
    $defaultGateway = old('payment_gateway', $payment['default_gateway'] ?? 'checkoutpay');
    $enabledGateways = old('enabled_gateways', $payment['enabled_gateways'] ?? ['checkoutpay', 'paystack']);
    $gatewayMeta = [
        'checkoutpay' => ['label' => 'CheckoutPay', 'note' => 'NGN & USD · bank transfer'],
        'paystack' => ['label' => 'Paystack', 'note' => 'NGN only · card, USSD, bank'],
    ];
@endphp

<div class="assist-admin-card glass-panel" id="payment-gateways">
    <h2>Payment gateways</h2>
    <p class="assist-text-muted mb-4">
        Enable one or more gateways. Customers choose at checkout when more than one is available for their currency.
    </p>

    <form method="POST" action="{{ $gatewaySaveRoute ?? route('admin.assist.system.payment-gateway') }}" class="assist-admin-form-grid" style="max-width: 720px; margin-bottom: 28px;">
        @csrf
        <div class="assist-field" style="grid-column: 1 / -1;">
            <span class="assist-field-label" style="display:block; margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--on-surface-variant);">Enabled gateways</span>
            <div class="flex gap-4" style="flex-wrap: wrap;">
                @foreach ($gatewayMeta as $id => $meta)
                    <label class="assist-checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="enabled_gateways[]" value="{{ $id }}"
                            @checked(in_array($id, $enabledGateways, true))>
                        <span>
                            <strong>{{ $meta['label'] }}</strong>
                            <span class="assist-text-muted" style="display: block; font-size: 12px;">{{ $meta['note'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="assist-field">
            <label for="payment_gateway">Default gateway</label>
            <select id="payment_gateway" name="payment_gateway" class="assist-select">
                @foreach ($gatewayMeta as $id => $meta)
                    <option value="{{ $id }}" @selected($defaultGateway === $id)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <p class="assist-text-muted" style="font-size: 12px; margin-top: 6px;">Used when the customer does not pick a specific method.</p>
        </div>
        <div>
            <button type="submit" class="assist-btn assist-btn-primary">Save gateway settings</button>
        </div>
    </form>

    <p class="assist-admin-section-title">CheckoutPay</p>
    <form method="POST" action="{{ $checkoutSaveRoute ?? route('admin.assist.system.checkout') }}" class="assist-admin-form-grid" style="max-width: 640px;">
        @csrf
        <x-assist.input label="API base URL" name="checkout_base_url" type="url" :value="old('checkout_base_url', $checkout['base_url'] ?? 'https://check-outpay.com/api/v1')" required />
        <x-assist.input label="API key (X-API-Key)" name="checkout_api_key" type="password"
            placeholder="{{ ($checkout['api_key_set'] ?? false) ? 'Leave blank to keep current key' : 'Enter API key' }}" />
        <x-assist.input label="Webhook URL" name="checkout_webhook_url" type="url" :value="old('checkout_webhook_url', $checkout['webhook_url'] ?? '')" required />
        <x-assist.input label="Developer program partner ID (optional)" name="checkout_dev_program_partner_id"
            :value="old('checkout_dev_program_partner_id', $checkout['dev_program_partner_id'] ?? '')" />
        <div>
            <button type="submit" class="assist-btn assist-btn-outline">Save CheckoutPay</button>
        </div>
    </form>

    <p class="assist-admin-section-title">Paystack</p>
    <form method="POST" action="{{ $paystackSaveRoute ?? route('admin.assist.system.paystack') }}" class="assist-admin-form-grid" style="max-width: 640px;">
        @csrf
        <x-assist.input label="Public key" name="paystack_public_key" :value="old('paystack_public_key', $paystack['public_key'] ?? '')" />
        <x-assist.input label="Secret key" name="paystack_secret_key" type="password"
            placeholder="{{ ($paystack['secret_key_set'] ?? false) ? 'Leave blank to keep current key' : 'Enter secret key' }}" />
        <x-assist.input label="Webhook URL" name="paystack_webhook_url" type="url" :value="old('paystack_webhook_url', $paystack['webhook_url'] ?? '')" required />
        <p class="assist-text-muted" style="font-size: 12px;">Register this URL in Paystack Dashboard → Settings → Webhooks. Event: <code>charge.success</code></p>
        <div>
            <button type="submit" class="assist-btn assist-btn-outline">Save Paystack</button>
        </div>
    </form>
</div>
