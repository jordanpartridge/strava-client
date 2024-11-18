<?php

return [
    'base_url' => env('STRAVA_BASE_URL', 'https://www.strava.com/api/v3'),
    'max_refresh_attempts' => env('STRAVA_CLIENT_MAX_REFRESH_ATTEMPTS', 3),
    'client_id' => env('STRAVA_CLIENT_ID', ''),
    'client_secret' => env('STRAVA_CLIENT_SECRET', ''),
    'scope' => env('STRAVA_CLIENT_SCOPE', 'read,activity:read_all'),
];
