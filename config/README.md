# Configuration Documentation

This directory contains the configuration file for the Laravel Strava Client package.

## Configuration File

- **strava-client.php**: The main configuration file that gets published to your Laravel application's config directory when installing the package.

## Configuration Options

### OAuth Endpoints
- `authorize_url`: The Strava API authorization URL
- `base_url`: The base URL for Strava API requests

### Authentication Credentials
- `client_id`: Your Strava API client ID (should be set in .env)
- `client_secret`: Your Strava API client secret (should be set in .env)

### Application Settings
- `scope`: The OAuth scopes to request from Strava (default: 'read,activity:read_all')
- `max_refresh_attempts`: Maximum number of token refresh attempts before failing (default: 3)
- `redirect_after_connect`: Where to redirect after successful Strava connection (default: '/admin')

## Environment Variables

The following environment variables should be set in your `.env` file:

```env
STRAVA_CLIENT_ID=your-client-id
STRAVA_CLIENT_SECRET=your-client-secret
```

Optional environment variables:

```env
STRAVA_CLIENT_SCOPE=read,activity:read_all
STRAVA_CLIENT_MAX_REFRESH_ATTEMPTS=3
STRAVA_CLIENT_REDIRECT_AFTER_CONNECT=/admin
```

## Publishing the Configuration

When you run the installation command, the configuration file will be published to your application:

```bash
php artisan strava-client:install
```

You can also publish it manually:

```bash
php artisan vendor:publish --tag="strava-client-config"
```