<?php

/**
 * Paste into bootstrap/app.php inside ->withMiddleware(function (Middleware $middleware) { ... })
 */
// $middleware->alias([
//     'assist.key' => \App\Http\Middleware\AssistApiKey::class,
//     'assist.setup' => \App\Http\Middleware\AssistSetupGate::class,
//     'assist.admin' => \App\Http\Middleware\EnsureAssistAdmin::class,
// ]);

/**
 * routes/web.php (after setup routes):
 *
 * require base_path('routes/assist-setup.php');
 * require base_path('routes/assist-web.php');
 *
 * Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
 *     require base_path('routes/assist-admin.php');
 * });
 */
