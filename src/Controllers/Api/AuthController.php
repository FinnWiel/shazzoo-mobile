<?php

namespace FinnWiel\ShazzooNotify\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use FinnWiel\ShazzooNotify\Models\DeviceNotificationPreference;
use FinnWiel\ShazzooNotify\Models\NotificationType;
use FinnWiel\ShazzooNotify\Models\RegisteredDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'token' => 'sometimes|nullable|string',
            'device_type' => 'sometimes|nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['email' => ['Invalid credentials.']],
            ], 422);
        }

        // Register the device
        $deviceData = [
            'user_id' => $user->id,
            'device_type' => $request->input('device_type', 'unknown'),
        ];

        if ($request->filled('token')) {
            $deviceData['token'] = $request->token;

            // Clean up this token from other users
            RegisteredDevice::where('token', $request->token)
                ->where('user_id', '!=', $user->id)
                ->delete();
        }

        $registeredDevice = RegisteredDevice::updateOrCreate(
            ['token' => $request->token, 'user_id' => $user->id],
            $deviceData
        );

        // Create the token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Create or update notification preferences
        $notificationTypes = NotificationType::all();
        foreach ($notificationTypes as $type) {
            Log::info($registeredDevice);
            DeviceNotificationPreference::updateOrCreate(
                [
                    'device_id' => $registeredDevice->id,
                    'notification_type_id' => $type->id,
                ],
                ['enabled' => true]
            );
        }

        return response()->json([
            'message' => 'Logged in',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();

        // Remove user's token record if it matches
        RegisteredDevice::where('user_id', $user->id)
            ->where('token', $request->token)
            ->delete();

        // Revoke the current token
        $currentTokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', $currentTokenId)->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
