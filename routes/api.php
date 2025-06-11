<?php

use FinnWiel\ShazzooMobile\Controllers\Api\AuthController;
use FinnWiel\ShazzooMobile\Controllers\Api\PreferenceController;
use FinnWiel\ShazzooMobile\Controllers\Api\NotificationTypeController;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use FinnWiel\ShazzooMobile\Notifications\BaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/notification-preferences', [PreferenceController::class, 'show']);
        Route::post('/notification-preferences', [PreferenceController::class, 'update']);
    });

    Route::middleware(['auth:sanctum'])->get('/me', function (Request $request) {
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(['user' => $request->user()]);
    });
});
