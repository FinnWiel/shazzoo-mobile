<?php

namespace FinnWiel\ShazzooMobile\Controllers\Api;

use App\Http\Controllers\Controller;
use FinnWiel\ShazzooMobile\Models\DeviceNotificationPreference;
use FinnWiel\ShazzooMobile\Models\ExpoToken;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    /**
     * Show the authenticated user's notification preferences.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $expoTokenValue = $request->header('Expo-Token') ?? $request->input('expo_token');

        if (!$expoTokenValue) {
            return response()->json(['message' => 'Expo token required'], 400);
        }

        // Find the ExpoToken record for this user and token
        $expoToken = ExpoToken::where('user_id', $user->id)
            ->where('token', $expoTokenValue)
            ->first();

        if (! $expoToken) {
            return response()->json(['message' => 'Expo token not found for user'], 404);
        }

        // Get all notification types
        $notificationTypes = NotificationType::all();

        // Get preferences for this expo token indexed by notification_type_id
        $preferences = DeviceNotificationPreference::where('expo_token_id', $expoToken->id)
            ->pluck('enabled', 'notification_type_id');

        // Map notification type names to enabled/disabled (default false)
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

        // Validate that each value is boolean
        foreach ($data as $key => $value) {
            if (!is_bool($value)) {
                return response()->json(['message' => "The value for {$key} must be boolean."], 422);
            }
        }

        $expoTokenValue = $request->header('Expo-Token') ?? $request->input('expo_token');

        if (!$expoTokenValue) {
            return response()->json(['message' => 'Expo token required'], 400);
        }

        $expoToken = ExpoToken::where('user_id', $user->id)
            ->where('token', $expoTokenValue)
            ->first();

        if (! $expoToken) {
            return response()->json(['message' => 'Expo token not found for user'], 404);
        }

        $types = NotificationType::pluck('id', 'name');

        foreach ($data as $typeName => $enabled) {
            if (!isset($types[$typeName])) {
                continue;
            }

            DeviceNotificationPreference::updateOrCreate(
                [
                    'expo_token_id' => $expoToken->id,
                    'notification_type_id' => $types[$typeName],
                ],
                ['enabled' => (bool) $enabled]
            );
        }

        // Retrieve updated preferences
        $notificationTypes = NotificationType::all();

        $preferences = DeviceNotificationPreference::where('expo_token_id', $expoToken->id)
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
