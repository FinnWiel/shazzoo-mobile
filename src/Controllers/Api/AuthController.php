<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Remove expo token from other users if it exists elsewhere
        User::where('expo_token', $request->expo_token)
            ->where('id', '!=', $user->id)
            ->update(['expo_token' => null]);

        // Set Expo token for current user
        $user->expo_token = $request->expo_token;
        $user->save();

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

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

        // Clear this user's expo token only if it matches
        if ($user->expo_token === $request->expo_token) {
            $user->expo_token = null;
            $user->save();
        }

        // Revoke all tokens (or just current token, up to you)
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
