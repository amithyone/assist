<?php

use App\Http\Controllers\Web\AssistAuthController;
use App\Http\Controllers\Web\AssistBillingController;
use App\Http\Controllers\Web\AssistPageController;
use App\Http\Controllers\Webhooks\CheckoutPayWebhookController;
use Illuminate\Support\Facades\Route;

/*
| Load routes/assist-setup.php before this file.
| Optionally wrap routes in: Route::middleware(['assist.setup:require'])->group(...)
*/

Route::post('/webhooks/checkoutpay', [CheckoutPayWebhookController::class, 'handle'])
    ->name('assist.webhooks.checkoutpay');

Route::get('/', [AssistPageController::class, 'home'])->name('assist.home');
Route::get('/pricing', [AssistPageController::class, 'pricing'])->name('assist.pricing');
Route::get('/docs', [AssistPageController::class, 'docs'])->name('assist.docs');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AssistAuthController::class, 'showLogin'])->name('assist.login');
    Route::post('/login', [AssistAuthController::class, 'login']);
    Route::get('/register', [AssistAuthController::class, 'showRegister'])->name('assist.register');
    Route::post('/register', [AssistAuthController::class, 'register']);
    Route::get('/forgot-password', [AssistAuthController::class, 'showForgotPassword'])->name('assist.password.request');
    Route::post('/forgot-password', [AssistAuthController::class, 'sendResetLink'])->name('assist.password.email');
    Route::get('/reset-password/{token}', [AssistAuthController::class, 'showResetPassword'])->name('assist.password.reset');
    Route::post('/reset-password', [AssistAuthController::class, 'resetPassword'])->name('assist.password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AssistAuthController::class, 'dashboard'])->name('assist.dashboard');
    Route::post('/logout', [AssistAuthController::class, 'logout'])->name('assist.logout');
    Route::get('/billing/upgrade/{plan}', [AssistBillingController::class, 'upgrade'])->name('assist.billing.upgrade');
    Route::get('/billing/payment/{transaction}', [AssistBillingController::class, 'payment'])->name('assist.billing.payment');
});
