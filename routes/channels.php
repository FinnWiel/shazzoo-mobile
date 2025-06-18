<?php

use Illuminate\Support\Facades\Broadcast;
use FinnWiel\ShazzooNotify\Models\RegisteredDevice;

Broadcast::channel('{type}', function ($user, $type, $request) {
    $token = $request->header('Device-Token') ?? $request->input('token');

    if (!$token) {
        return false;
    }

    return RegisteredDevice::where('user_id', $user->id)
        ->where('token', $token)
        ->exists();
});
