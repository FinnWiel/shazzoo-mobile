<?php

namespace FinnWiel\ShazzooMobile\Controllers\Api;

use App\Http\Controllers\Controller;
use FinnWiel\ShazzooMobile\Models\DeviceNotificationPreference;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use FinnWiel\ShazzooMobile\Models\RegisteredDevice;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    /**
     * Show the authenticated user's notification preferences.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $tokenValue = $request->header('Expo-Token') ?? $request->input('token');

        if (!$tokenValue) {
            return response()->json(['message' => 'Token required'], 400);
        }

        $device = RegisteredDevice::where('user_id', $user->id)
            ->where('token', $tokenValue)
            ->first();

        if (! $device) {
            return response()->json(['message' => 'Token not found for user'], 404);
        }

        $notificationTypes = NotificationType::all();

        $preferences = DeviceNotificationPreference::where('device_id', $device->id)
            ->pluck('enabled', 'notification_type_id');

        $result = $notificationTypes->mapWithKeys(function ($type) use ($preferences) {
            return [$type->name => (bool) ($preferences[$type->id] ?? false)];
        });

        return response()->json($result);
    }

    /**
     * Update the authenticated user's notification preferences.
     *
     * Expects JSON with keys as notification type names and boolean values.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->all();

        foreach ($data as $key => $value) {
            if (!is_bool($value)) {
                return response()->json(['message' => "The value for {$key} must be boolean."], 422);
            }
        }

        $tokenValue = $request->header('Expo-Token') ?? $request->input('token');

        if (!$tokenValue) {
            return response()->json(['message' => 'Token required'], 400);
        }

        $device = RegisteredDevice::where('user_id', $user->id)
            ->where('token', $tokenValue)
            ->first();

        if (! $device) {
            return response()->json(['message' => 'Token not found for user'], 404);
        }

        $types = NotificationType::pluck('id', 'name');

        foreach ($data as $typeName => $enabled) {
            if (!isset($types[$typeName])) {
                continue;
            }

            DeviceNotificationPreference::updateOrCreate(
                [
                    'device_id' => $device->id,
                    'notification_type_id' => $types[$typeName],
                ],
                ['enabled' => $enabled]
            );
        }

        $notificationTypes = NotificationType::all();
        $preferences = DeviceNotificationPreference::where('device_id', $device->id)
            ->pluck('enabled', 'notification_type_id');

        $result = $notificationTypes->mapWithKeys(function ($type) use ($preferences) {
            return [$type->name => (bool) ($preferences[$type->id] ?? false)];
        });

        return response()->json([
            'message' => 'Preferences updated successfully.',
            'preferences' => $result,
        ]);
    }
}
