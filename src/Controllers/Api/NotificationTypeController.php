<?php

namespace FinnWiel\ShazzooMobile\Controllers\Api;

use FinnWiel\ShazzooMobile\Models\NotificationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class NotificationTypeController extends Controller
{
    /**
     * Get all available notification types.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get types from config
        $configTypes = Config::get('shazzoo-mobile.notifications.types', []);
        
        // Get types from database
        $dbTypes = NotificationType::all()->keyBy('name')->toArray();
        
        // Merge config and database types, with database taking precedence
        $types = array_merge($configTypes, $dbTypes);
        
        return response()->json([
            'types' => $types,
            'channels' => collect($types)->keys()->map(function ($type) {
                return Config::get('shazzoo-mobile.notifications.websocket.channel_prefix') . $type;
            })->values(),
        ]);
    }

    /**
     * Sync notification types from config to database.
     *
     * @return JsonResponse
     */
    public function sync(): JsonResponse
    {
        $configTypes = Config::get('shazzoo-mobile.notifications.types', []);
        
        foreach ($configTypes as $type => $data) {
            NotificationType::updateOrCreate(
                ['name' => $type],
                [
                    'name' => $type,
                    'description' => $data['description'] ?? null,
                ]
            );
        }
        
        return response()->json([
            'message' => 'Notification types synchronized successfully',
            'types' => NotificationType::all(),
        ]);
    }
} 