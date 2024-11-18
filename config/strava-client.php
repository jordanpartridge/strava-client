<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Strava API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Strava API integration. These settings control the
    | OAuth flow and API access for your application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | OAuth Endpoints
    |--------------------------------------------------------------------------
    */
    'authorize_url' => env('STRAVA_AUTHORIZE_URL', 'https://www.strava.com/oauth/authorize'),
    'base_url' => env('STRAVA_BASE_URL', 'https://www.strava.com/api/v3'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Credentials
    |--------------------------------------------------------------------------
    */
    'client_id' => env('STRAVA_CLIENT_ID', ''),
    'client_secret' => env('STRAVA_CLIENT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    */
    'scope' => env('STRAVA_CLIENT_SCOPE', 'read,activity:read_all'),
    'max_refresh_attempts' => env('STRAVA_CLIENT_MAX_REFRESH_ATTEMPTS', 3),
    'redirect_after_connect' => env('STRAVA_CLIENT_REDIRECT_AFTER_CONNECT', '/admin'),
];
