<?php

return [
    'driver' => env('MAP_DRIVER', 'google'),

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

