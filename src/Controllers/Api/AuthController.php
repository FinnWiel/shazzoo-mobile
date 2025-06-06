<?php

namespace FinnWiel\ShazzooMobile\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use FinnWiel\ShazzooMobile\Models\DeviceNotificationPreference;
use FinnWiel\ShazzooMobile\Models\ExpoToken;
use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'expo_token' => 'required|string',
            'device_type' => 'sometimes|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'errors' => [
                    'email' => ['We couldn’t find a user with that email address.'],
                ]
            ], 422);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['Incorrect password.'],
                ]
            ], 422);
        }

        // Remove expo token from other users if it exists elsewhere
        ExpoToken::where('token', $request->expo_token)
            ->where('user_id', '!=', $user->id)
            ->delete();

        $expoTokenData = [
            'token' => $request->expo_token,
            'user_id' => $user->id,
        ];

        if ($request->has('device_type')) {
            $expoTokenData['device_type'] = $request->device_type;
        }

        // Update or create expo token for current user
        $expoToken = ExpoToken::updateOrCreate(
            ['token' => $request->expo_token, 'user_id' => $user->id],
            $expoTokenData
        );

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        $notificationTypes = NotificationType::all();

        foreach ($notificationTypes as $type) {
            DeviceNotificationPreference::updateOrCreate(
                [
                    'expo_token_id' => $expoToken->id,
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
            'expo_token' => 'required|string',
        ]);

        $user = $request->user();

        // Remove user's expo token record if it matches
        ExpoToken::where('user_id', $user->id)
            ->where('token', $request->expo_token)
            ->delete();

        // Revoke all tokens (or current token only)
        $currentTokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', $currentTokenId)->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
