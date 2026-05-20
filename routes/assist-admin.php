<?php

use App\Http\Controllers\Admin\AssistActivityAdminController;
use App\Http\Controllers\Admin\AssistDashboardAdminController;
use App\Http\Controllers\Admin\AssistDownloadAdminController;
use App\Http\Controllers\Admin\AssistSystemAdminController;
use Illuminate\Support\Facades\Route;

/*
| Paste into routes/web.php inside your admin middleware group:
|
| Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
|     require base_path('routes/assist-admin.php');
| });
*/

Route::get('/', [AssistDashboardAdminController::class, 'index'])->name('admin.assist.dashboard');
Route::get('users', [AssistDashboardAdminController::class, 'users'])->name('admin.assist.users');
Route::post('users/plan', [AssistDashboardAdminController::class, 'updateUserPlan'])->name('admin.assist.users.plan');

Route::get('downloads', [AssistDownloadAdminController::class, 'index'])->name('admin.assist.downloads');
Route::post('downloads', [AssistDownloadAdminController::class, 'store'])->name('admin.assist.downloads.store');
Route::delete('downloads', [AssistDownloadAdminController::class, 'destroy'])->name('admin.assist.downloads.destroy');

Route::get('activity', [AssistActivityAdminController::class, 'index'])->name('admin.assist.activity');
Route::get('activity/export', [AssistActivityAdminController::class, 'exportCsv'])->name('admin.assist.activity.export');

Route::get('system', [AssistSystemAdminController::class, 'index'])->name('admin.assist.system');
Route::post('system/database', [AssistSystemAdminController::class, 'saveDatabase'])->name('admin.assist.system.database');
Route::post('system/migrate', [AssistSystemAdminController::class, 'migrate'])->name('admin.assist.system.migrate');
Route::post('system/seed', [AssistSystemAdminController::class, 'seed'])->name('admin.assist.system.seed');
