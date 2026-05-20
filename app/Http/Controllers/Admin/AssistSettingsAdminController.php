<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AssistInstallerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistSettingsAdminController extends Controller
{
    public function __construct(
        protected AssistInstallerService $installer
    ) {}

    public function index(): View
    {
        return view('admin.assist-settings.index', [
            'payment' => $this->installer->paymentSettingsForAdmin(),
            'app' => [
                'url' => config('app.url'),
                'assist_app_key' => config('assist.app_key') ? '••••••••' : '',
                'assist_app_key_set' => (bool) config('assist.app_key'),
                'assist_app_key_plain' => (bool) config('assist.app_key'),
                'support_email' => config('assist.support_email'),
                'download_url' => config('assist.download_url'),
            ],
        ]);
    }

    public function savePaymentGateway(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_gateway' => 'required|in:checkoutpay,paystack',
            'enabled_gateways' => 'nullable|array',
            'enabled_gateways.*' => 'in:checkoutpay,paystack',
        ]);

        $enabled = $data['enabled_gateways'] ?? [];
        $this->installer->savePaymentGateways($enabled, $data['payment_gateway']);
        $this->installer->refreshConfig();

        $count = count($enabled);

        return back()->with(
            'status',
            $count > 1
                ? "{$count} payment gateways enabled. Default: {$data['payment_gateway']}."
                : 'Payment gateway settings saved.'
        );
    }

    public function savePaystack(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paystack_public_key' => 'nullable|string|max:255',
            'paystack_secret_key' => 'nullable|string|max:255',
            'paystack_webhook_url' => 'required|url|max:500',
        ]);

        $paystack = [
            'public_key' => $data['paystack_public_key'] ?? '',
            'webhook_url' => $data['paystack_webhook_url'],
        ];
        if (! empty($data['paystack_secret_key']) && ! str_contains($data['paystack_secret_key'], '••')) {
            $paystack['secret_key'] = $data['paystack_secret_key'];
        }

        $this->installer->savePaystackEnvironment($paystack);
        $this->installer->refreshConfig();

        return back()->with('status', 'Paystack settings saved.');
    }

    public function saveCheckout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'checkout_base_url' => 'required|url|max:255',
            'checkout_api_key' => 'nullable|string|max:255',
            'checkout_webhook_url' => 'required|url|max:500',
            'checkout_dev_program_partner_id' => 'nullable|integer',
        ]);

        $checkout = [
            'base_url' => $data['checkout_base_url'],
            'webhook_url' => $data['checkout_webhook_url'],
            'dev_program_partner_id' => $data['checkout_dev_program_partner_id'] ?? '',
        ];

        if (! empty($data['checkout_api_key']) && ! str_contains($data['checkout_api_key'], '••')) {
            $checkout['api_key'] = $data['checkout_api_key'];
        }

        $this->installer->saveCheckoutEnvironment($checkout);
        $this->installer->refreshConfig();

        return back()->with('status', 'CheckoutPay settings saved.');
    }

    public function saveApp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_url' => 'required|url|max:255',
            'assist_app_key' => 'nullable|string|min:16|max:255',
            'support_email' => 'nullable|email|max:255',
            'download_url' => 'nullable|string|max:255',
        ]);

        $app = [
            'url' => rtrim($data['app_url'], '/'),
            'support_email' => $data['support_email'] ?? config('assist.support_email'),
            'download_url' => $data['download_url'] ?? config('assist.download_url'),
            'upgrade_url' => rtrim($data['app_url'], '/').'/pricing',
        ];

        if (! empty($data['assist_app_key']) && ! str_contains($data['assist_app_key'], '••')) {
            $app['assist_app_key'] = $data['assist_app_key'];
        }

        $this->installer->saveEnvironment([], $app);
        $this->installer->refreshConfig();

        return back()->with('status', 'Application settings saved.');
    }
}
