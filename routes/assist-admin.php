<?php

use App\Http\Controllers\Admin\AssistActivityAdminController;
use App\Http\Controllers\Admin\AssistDashboardAdminController;
use App\Http\Controllers\Admin\AssistDownloadAdminController;
use App\Http\Controllers\Admin\AssistPlansAdminController;
use App\Http\Controllers\Admin\AssistSettingsAdminController;
use App\Http\Controllers\Admin\AssistSitePagesAdminController;
use App\Http\Controllers\Admin\AssistSystemAdminController;
use App\Http\Controllers\Admin\AssistUsersAdminController;
use App\Http\Controllers\Admin\AssistVouchersAdminController;
use Illuminate\Support\Facades\Route;

/*
| Paste into routes/web.php inside your admin middleware group:
|
| Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
|     require base_path('routes/assist-admin.php');
| });
*/

Route::get('/', [AssistDashboardAdminController::class, 'index'])->name('admin.assist.dashboard');

Route::get('site-pages', [AssistSitePagesAdminController::class, 'index'])->name('admin.assist.site-pages');
Route::get('site-pages/{sitePage}/edit', [AssistSitePagesAdminController::class, 'edit'])->name('admin.assist.site-pages.edit');
Route::put('site-pages/{sitePage}', [AssistSitePagesAdminController::class, 'update'])->name('admin.assist.site-pages.update');

Route::get('plans', [AssistPlansAdminController::class, 'index'])->name('admin.assist.plans');
Route::get('plans/{plan}/edit', [AssistPlansAdminController::class, 'edit'])->name('admin.assist.plans.edit');
Route::put('plans/{plan}', [AssistPlansAdminController::class, 'update'])->name('admin.assist.plans.update');

Route::get('vouchers', [AssistVouchersAdminController::class, 'index'])->name('admin.assist.vouchers');
Route::post('vouchers', [AssistVouchersAdminController::class, 'store'])->name('admin.assist.vouchers.store');
Route::put('vouchers/{voucher}', [AssistVouchersAdminController::class, 'update'])->name('admin.assist.vouchers.update');
Route::delete('vouchers/{voucher}', [AssistVouchersAdminController::class, 'destroy'])->name('admin.assist.vouchers.destroy');

Route::get('users', [AssistUsersAdminController::class, 'index'])->name('admin.assist.users');
Route::put('users/{user}', [AssistUsersAdminController::class, 'update'])->name('admin.assist.users.update');
Route::put('users/{user}/password', [AssistUsersAdminController::class, 'updatePassword'])->name('admin.assist.users.password');
Route::put('account/password', [AssistUsersAdminController::class, 'updateOwnPassword'])->name('admin.assist.account.password');

Route::get('settings', [AssistSettingsAdminController::class, 'index'])->name('admin.assist.settings');
Route::post('settings/gateway', [AssistSettingsAdminController::class, 'savePaymentGateway'])->name('admin.assist.settings.gateway');
Route::post('settings/checkout', [AssistSettingsAdminController::class, 'saveCheckout'])->name('admin.assist.settings.checkout');
Route::post('settings/paystack', [AssistSettingsAdminController::class, 'savePaystack'])->name('admin.assist.settings.paystack');
Route::post('settings/app', [AssistSettingsAdminController::class, 'saveApp'])->name('admin.assist.settings.app');

Route::get('downloads', [AssistDownloadAdminController::class, 'index'])->name('admin.assist.downloads');
Route::post('downloads', [AssistDownloadAdminController::class, 'store'])->name('admin.assist.downloads.store');
Route::delete('downloads', [AssistDownloadAdminController::class, 'destroy'])->name('admin.assist.downloads.destroy');

Route::get('activity', [AssistActivityAdminController::class, 'index'])->name('admin.assist.activity');
Route::get('activity/export', [AssistActivityAdminController::class, 'exportCsv'])->name('admin.assist.activity.export');

Route::get('system', [AssistSystemAdminController::class, 'index'])->name('admin.assist.system');
Route::post('system/payment-gateway', [AssistSystemAdminController::class, 'savePaymentGateway'])->name('admin.assist.system.payment-gateway');
Route::post('system/checkout', [AssistSystemAdminController::class, 'saveCheckout'])->name('admin.assist.system.checkout');
Route::post('system/paystack', [AssistSystemAdminController::class, 'savePaystack'])->name('admin.assist.system.paystack');
Route::post('system/database', [AssistSystemAdminController::class, 'saveDatabase'])->name('admin.assist.system.database');
Route::post('system/migrate', [AssistSystemAdminController::class, 'migrate'])->name('admin.assist.system.migrate');
Route::post('system/seed', [AssistSystemAdminController::class, 'seed'])->name('admin.assist.system.seed');
