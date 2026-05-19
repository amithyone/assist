<?php

namespace App\Http\Middleware;

use App\Services\AssistInstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssistSetupGate
{
    public function __construct(
        protected AssistInstallerService $installer
    ) {}

    /**
     * Block setup routes once installed; redirect guests to setup when not installed.
     */
    public function handle(Request $request, Closure $next, string $mode = 'setup'): Response
    {
        $installed = $this->installer->isInstalled();

        if ($mode === 'setup' && $installed) {
            return redirect()->route('assist.home')
                ->with('status', 'Assist is already installed.');
        }

        if ($mode === 'require' && ! $installed && config('assist.setup_enabled', true)) {
            return redirect()->route('assist.setup.index');
        }

        return $next($request);
    }
}
