<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\AssistInstallerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistSetupController extends Controller
{
    public function __construct(
        protected AssistInstallerService $installer
    ) {}

    public function index(): View
    {
        return view('setup.index', [
            'requirements' => $this->installer->requirements(),
            'requirementsMet' => $this->installer->requirementsMet(),
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
            'seed_test_user' => 'boolean',
        ]);

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
                    'url' => $data['app_url'],
                    'assist_app_key' => $data['assist_app_key'],
                    'support_email' => $data['support_email'] ?? 'support@assist.app',
                    'download_url' => $data['download_url'] ?? '#download',
                ],
                (bool) ($data['seed_test_user'] ?? false)
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['install' => $e->getMessage()]);
        }

        return redirect()
            ->route('assist.home')
            ->with('status', 'Assist installed successfully. You can log in or register.');
    }
}
