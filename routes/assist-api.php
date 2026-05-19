<?php

use App\Http\Controllers\Api\Assist\ActivityController;
use App\Http\Controllers\Api\Assist\AuthController;
use App\Http\Controllers\Api\Assist\LicenseController;
use App\Http\Controllers\Api\Assist\UsageController;
use App\Http\Middleware\AssistApiKey;
use Illuminate\Support\Facades\Route;

/*
| Paste into routes/api.php:
|
| require base_path('routes/assist-api.php');
| Or copy the Route::prefix block below.
*/

Route::prefix('assist')
    ->middleware([AssistApiKey::class, 'throttle:60,1'])
    ->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:20,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::get('limits', [LicenseController::class, 'limits']);
            Route::post('usage/check', [UsageController::class, 'check']);
            Route::post('usage/record', [UsageController::class, 'record']);
            Route::get('activity', [ActivityController::class, 'index']);
            Route::post('activity/sync', [ActivityController::class, 'sync']);
        });
    });
