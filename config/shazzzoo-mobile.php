<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Notification Types
    |--------------------------------------------------------------------------
    |
    | Define the notification types that your application supports.
    | Each type should have a unique identifier and a display name.
    |
    */
    'types' => [
        'default' => [
            'name' => 'Default',
            'description' => 'Default notification type',
        ],
        // Add more notification types here as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | WebSocket Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WebSocket broadcasting of notifications.
    | Supported drivers: pusher, reverb, ably, redis, log
    |
    */
    'websocket' => [
        'channel_prefix' => 'notifications.',
        'driver' => env('BROADCAST_DRIVER', 'reverb'),
        'reverb' => [
            'host' => env('REVERB_HOST', '127.0.0.1'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
        ],
    ],
]; 