<?php

use App\Http\Controllers\Setup\AssistSetupController;
use App\Http\Middleware\AssistSetupGate;
use Illuminate\Support\Facades\Route;

/*
| First-time installer (public). Register middleware in host app:
| 'assist.setup' => \App\Http\Middleware\AssistSetupGate::class,
|
| In routes/web.php (before other assist routes):
| require base_path('routes/assist-setup.php');
*/

Route::middleware(['assist.setup:setup'])->prefix('assist/setup')->name('assist.setup.')->group(function () {
    Route::get('/', [AssistSetupController::class, 'index'])->name('index');
    Route::post('/test-database', [AssistSetupController::class, 'testDatabase'])->name('test-database');
    Route::post('/composer', [AssistSetupController::class, 'runComposer'])->name('composer');
    Route::post('/install', [AssistSetupController::class, 'install'])->name('install');
});
