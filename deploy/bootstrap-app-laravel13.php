<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'assist.key' => \App\Http\Middleware\AssistApiKey::class,
            'assist.setup' => \App\Http\Middleware\AssistSetupGate::class,
            'assist.admin' => \App\Http\Middleware\EnsureAssistAdmin::class,
        ]);
        $middleware->redirectGuestsTo('/login');
        $middleware->validateCsrfTokens(except: [
            'webhooks/checkoutpay',
            'webhooks/paystack',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
