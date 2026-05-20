<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\AssistInstallerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssistSetupController extends Controller
{
    public function __construct(
        protected AssistInstallerService $installer
    ) {}

    public function index(): View
    {
        $appUrl = rtrim(old('app_url', url('/')), '/');

        return view('setup.index', [
            'requirements' => $this->installer->requirements(),
            'requirementsMet' => $this->installer->requirementsMet(),
            'vendorPresent' => $this->installer->vendorPresent(),
            'payment' => $this->installer->paymentSettingsForAdmin(),
        ]);
    }

    public function testDatabase(Request $request): JsonResponse
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string',
        ]);

        $result = $this->installer->testDatabaseConnection([
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
            'password' => $data['db_password'] ?? '',
        ]);

        return response()->json($result);
    }

    public function runComposer(): JsonResponse
    {
        $result = $this->installer->runComposerInstall();

        return response()->json($result);
    }

    public function install(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string',
            'app_url' => 'required|url|max:255',
            'assist_app_key' => 'required|string|min:16|max:255',
            'support_email' => 'nullable|email|max:255',
            'download_url' => 'nullable|string|max:255',
            'mail_mailer' => 'nullable|string|max:32',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|max:16',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'checkout_base_url' => 'nullable|url|max:255',
            'checkout_api_key' => 'nullable|string|max:255',
            'checkout_webhook_url' => 'nullable|url|max:500',
            'checkout_dev_program_partner_id' => 'nullable|integer',
            'payment_gateway' => 'nullable|in:checkoutpay,paystack',
            'enabled_gateways' => 'nullable|array',
            'enabled_gateways.*' => 'in:checkoutpay,paystack',
            'paystack_public_key' => 'nullable|string|max:255',
            'paystack_secret_key' => 'nullable|string|max:255',
            'paystack_webhook_url' => 'nullable|url|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
            'seed_test_user' => 'boolean',
            'fresh_install' => 'boolean',
        ]);

        $appUrl = rtrim($data['app_url'], '/');

        try {
            $this->installer->install(
                [
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => $data['db_database'],
                    'username' => $data['db_username'],
                    'password' => $data['db_password'] ?? '',
                ],
                [
                    'url' => $appUrl,
                    'assist_app_key' => $data['assist_app_key'],
                    'support_email' => $data['support_email'] ?? 'support@assist.app',
                    'download_url' => $data['download_url'] ?? '#download',
                    'upgrade_url' => $appUrl.'/pricing',
                ],
                [
                    'mailer' => $data['mail_mailer'] ?? 'smtp',
                    'host' => $data['mail_host'] ?? '',
                    'port' => $data['mail_port'] ?? '587',
                    'username' => $data['mail_username'] ?? '',
                    'password' => $data['mail_password'] ?? '',
                    'encryption' => $data['mail_encryption'] ?? 'tls',
                    'from_address' => $data['mail_from_address'] ?? ($data['support_email'] ?? 'noreply@example.com'),
                    'from_name' => $data['mail_from_name'] ?? 'Assist',
                ],
                [
                    'base_url' => $data['checkout_base_url'] ?? 'https://check-outpay.com/api/v1',
                    'api_key' => $data['checkout_api_key'] ?? '',
                    'webhook_url' => $data['checkout_webhook_url'] ?? ($appUrl.'/webhooks/checkoutpay'),
                    'dev_program_partner_id' => $data['checkout_dev_program_partner_id'] ?? '',
                ],
                [
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'],
                    'password' => $data['admin_password'],
                ],
                (bool) ($data['seed_test_user'] ?? false),
                (bool) ($data['fresh_install'] ?? true)
            );

            $this->installer->savePaymentGateways(
                $data['enabled_gateways'] ?? ['checkoutpay', 'paystack'],
                $data['payment_gateway'] ?? 'checkoutpay'
            );
            $this->installer->savePaystackEnvironment([
                'public_key' => $data['paystack_public_key'] ?? '',
                'secret_key' => $data['paystack_secret_key'] ?? '',
                'webhook_url' => $data['paystack_webhook_url'] ?? ($appUrl.'/webhooks/paystack'),
            ]);
            $this->installer->refreshConfig();
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['install' => $e->getMessage()]);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Assist installed successfully. Log in with your admin account.');
    }
}
