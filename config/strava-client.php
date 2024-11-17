<?php

return [
    'base_url' => env('STRAVA_BASE_URL', 'https://www.strava.com/api/v3'),
    'max_retries' => env('STRAVA_CLIENT_MAX_RETRIES', 3),
];
