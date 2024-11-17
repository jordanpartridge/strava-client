<?php

return [
    'base_url' => env('STRAVA_BASE_URL', 'https://www.strava.com/api/v3'),
    'max_refresh_attempts' => env('STRAVA_CLIENT_MAX_REFRESH_ATTEMPTS', 3),
];
