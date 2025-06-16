<?php

use FinnWiel\ShazzooMobile\Controllers\Api\AuthController;
use FinnWiel\ShazzooMobile\Controllers\Api\PreferenceController;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/notification-preferences', [PreferenceController::class, 'show']);
        Route::post('/notification-preferences', [PreferenceController::class, 'update']);
    });

    Route::get('/notification-types', function () {
        return response()->json(NotificationType::all());
    });

    Route::middleware(['auth:sanctum'])->get('/me', function (Request $request) {
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(['user' => $request->user()]);
    });

    Route::middleware('auth:sanctum')->get('/websocket-config', function () {
        $config = config('broadcasting.connections.reverb');

        return response()->json([
            'wsHost' => $config['options']['host'] ?? '127.0.0.1',
            'wsPort' => $config['options']['port'] ?? 8080,
            'forceTLS' => ($config['options']['scheme'] ?? 'http') === 'https',
            'pusherKey' => $config['key'] ?? null,
        ]);
    });
});
