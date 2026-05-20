<?php

use App\Http\Controllers\Web\AssistAuthController;
use App\Http\Controllers\Web\AssistBillingController;
use App\Http\Controllers\Web\AssistDownloadController;
use App\Http\Controllers\Web\AssistPageController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Webhooks\CheckoutPayWebhookController;
use App\Http\Controllers\Webhooks\PaystackWebhookController;
use Illuminate\Support\Facades\Route;

/*
| Load routes/assist-setup.php before this file.
| Optionally wrap routes in: Route::middleware(['assist.setup:require'])->group(...)
*/

Route::post('/webhooks/checkoutpay', [CheckoutPayWebhookController::class, 'handle'])
    ->name('assist.webhooks.checkoutpay');

Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])
    ->name('assist.webhooks.paystack');

Route::get('/download/assist/{platform}', [AssistDownloadController::class, 'downloadPlatform'])
    ->where('platform', 'mac_arm64|mac_x86_64|windows|linux')
    ->name('assist.download.platform');
Route::get('/download/assist', [AssistDownloadController::class, 'download'])->name('assist.download.app');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('assist.sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('assist.robots');

Route::get('/', [AssistPageController::class, 'home'])->name('assist.home');
Route::get('/pricing', [AssistPageController::class, 'pricing'])->name('assist.pricing');
Route::get('/docs', [AssistPageController::class, 'docs'])->name('assist.docs');
Route::get('/privacy', [AssistPageController::class, 'privacy'])->name('assist.privacy');
Route::get('/terms', [AssistPageController::class, 'terms'])->name('assist.terms');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AssistAuthController::class, 'showLogin'])->name('login');
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
