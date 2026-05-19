<?php

use App\Http\Controllers\Admin\AssistActivityAdminController;
use App\Http\Controllers\Admin\AssistSystemAdminController;
use Illuminate\Support\Facades\Route;

/*
| Paste into routes/web.php inside your admin middleware group:
|
| Route::middleware(['auth', 'admin'])->prefix('admin/assist')->group(function () {
|     require base_path('routes/assist-admin.php');
| });
|
| Or register routes individually:
*/

Route::get('activity', [AssistActivityAdminController::class, 'index'])->name('admin.assist.activity');
Route::get('activity/export', [AssistActivityAdminController::class, 'exportCsv'])->name('admin.assist.activity.export');

Route::get('system', [AssistSystemAdminController::class, 'index'])->name('admin.assist.system');
Route::post('system/database', [AssistSystemAdminController::class, 'saveDatabase'])->name('admin.assist.system.database');
Route::post('system/migrate', [AssistSystemAdminController::class, 'migrate'])->name('admin.assist.system.migrate');
Route::post('system/seed', [AssistSystemAdminController::class, 'seed'])->name('admin.assist.system.seed');
