<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AssistInstallerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistSystemAdminController extends Controller
{
    public function __construct(
        protected AssistInstallerService $installer
    ) {}

    public function index(): View
    {
        $status = $this->installer->migrationStatus();

        return view('admin.assist-system.index', [
            'installed' => $this->installer->isInstalled(),
            'lockFile' => $this->installer->lockFilePath(),
            'migrationStatus' => $status,
            'requirements' => $this->installer->requirements(),
        ]);
    }

    public function migrate(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'accepted',
        ]);

        try {
            $result = $this->installer->runMigrations();
            if ($this->installer->isInstalled() === false) {
                $this->installer->markInstalled();
            }

            return back()->with('status', 'Migrations completed.')->with('migrate_output', $result['output']);
        } catch (\Throwable $e) {
            return back()->withErrors(['migrate' => $e->getMessage()]);
        }
    }

    public function seed(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'accepted',
        ]);

        try {
            $result = $this->installer->runSeeders($request->boolean('include_test_user'));

            return back()->with('status', 'Seeders completed.')->with('seed_output', $result['output']);
        } catch (\Throwable $e) {
            return back()->withErrors(['seed' => $e->getMessage()]);
        }
    }

    public function saveDatabase(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string',
        ]);

        $test = $this->installer->testDatabaseConnection([
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
            'password' => $data['db_password'] ?? '',
        ]);

        if (! $test['ok']) {
            return back()->withInput()->withErrors(['db' => $test['message']]);
        }

        $db = [
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
        ];
        if (($data['db_password'] ?? '') !== '') {
            $db['password'] = $data['db_password'];
        }
        $this->installer->saveEnvironment($db);
        $this->installer->refreshConfig();

        return back()->with('status', 'Database credentials saved to .env. Run migrations when ready.');
    }
}
