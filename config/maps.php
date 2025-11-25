<?php

return [
    'driver' => env('MAP_DRIVER', 'iran'),

    // Primary tile URL template for Leaflet/OSM-like providers
    'tile_url' => env('MAP_TILE_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),

    // Optional pipe-separated list of fallback tile URLs
    'tile_urls' => array_values(array_filter(explode('|', env('MAP_TILE_URLS', '')))),

    'google' => [
        'api_key_server' => env('MAP_API_KEY_SERVER'),
        'api_key_client' => env('MAP_API_KEY'),
    ],

    'neshan' => [
        'api_key' => env('NESHAN_API_KEY'),
        'base_url' => env('NESHAN_BASE_URL', 'https://api.neshan.org'),
    ],

    'default_center' => [
        'lat' => env('MAP_DEFAULT_LAT', 35.6892),
        'lng' => env('MAP_DEFAULT_LNG', 51.3890),
        'zoom' => env('MAP_DEFAULT_ZOOM', 12),
    ],
];

