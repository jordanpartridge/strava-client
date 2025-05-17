# Routes Documentation

This directory contains the route definitions for the Laravel Strava Client package.

## Route Files

- **strava.php**: Defines the routes needed for Strava OAuth authentication.

## Available Routes

The package registers the following routes:

1. **GET /strava/authorize**
   - Handled by `RedirectController`
   - Initiates the OAuth flow by redirecting to Strava
   - Requires authenticated user

2. **GET /strava/callback**
   - Handled by `CallBackController`
   - Processes the callback from Strava after authorization
   - Exchanges authorization code for tokens
   - Stores tokens in the database
   - Redirects to the configured post-connection URL

## Authentication

Both routes assume the user is already authenticated in your Laravel application. The package uses the authenticated user to associate Strava tokens.

## Customization

You can customize the redirect URL after successful connection in the config file:

```php
// config/strava-client.php
'redirect_after_connect' => env('STRAVA_CLIENT_REDIRECT_AFTER_CONNECT', '/admin')
```

## Route Registration

These routes are automatically registered by the package's service provider when the package is booted. You don't need to manually register them in your application's route files.