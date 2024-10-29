# Laravel Strava Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)

A robust and developer-friendly Laravel package for interacting with the Strava API. Built with [Saloon](https://github.com/saloonphp/saloon), this package provides an elegant way to integrate Strava's features into your Laravel application.

## Features

- 🔒 OAuth 2.0 authentication flow support
- 🚴‍♂️ Easy access to Strava activities
- ♻️ Automatic token refresh handling
- 🎯 Type-safe requests and responses
- 🛠️ Built on top of Saloon HTTP client
- 🔧 Highly configurable and extensible
- 📦 Laravel integration out of the box

## Requirements

- PHP 8.2 or higher
- Laravel 10.0 or higher
- Strava API credentials

## Installation

Install the package via Composer:

```bash
composer require jordanpartridge/strava-client
```

## Configuration

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag="strava-client-config"
```

2. Add your Strava API credentials to your `.env` file:

```env
STRAVA_CLIENT_ID=your-client-id
STRAVA_CLIENT_SECRET=your-client-secret
```

3. Configure your Laravel services config (`config/services.php`):

```php
'strava' => [
    'client_id' => env('STRAVA_CLIENT_ID'),
    'client_secret' => env('STRAVA_CLIENT_SECRET'),
],
```

## Basic Usage

### Authentication

```php
use JordanPartridge\StravaClient\Facades\StravaClient;

// Exchange authorization code for tokens
$tokens = StravaClient::exchangeToken($authorizationCode);

// Set tokens for subsequent requests
StravaClient::setToken($tokens['access_token'], $tokens['refresh_token']);
```

### Fetching Activities

```php
// Get athlete activities (paginated)
$activities = StravaClient::activityForAthlete(page: 1, per_page: 30);

// Get a specific activity
$activity = StravaClient::getActivity($activityId);
```

## Advanced Usage

### Using the Client Directly

```php
use JordanPartridge\StravaClient\StravaClient;
use JordanPartridge\StravaClient\Connector;

$connector = new Connector();
$client = new StravaClient($connector);

// Set authentication tokens
$client->setToken($accessToken, $refreshToken);

// Make requests
$activities = $client->activityForAthlete(1, 30);
```

### Handling Token Refresh

The client automatically handles token refresh when an unauthorized response is received. You don't need to manually refresh tokens, but you can access the refresh functionality if needed:

```php
$connector->refreshToken();
```

## Error Handling

The package throws exceptions for various error cases:

```php
use Saloon\Exceptions\Request\RequestException;

try {
    $activity = StravaClient::getActivity($id);
} catch (RequestException $e) {
    // Handle API errors
    $status = $e->getResponse()->status();
    $message = $e->getMessage();
}
```

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Run code style fixes: `composer format`

## Security

If you discover any security-related issues, please email jordan@partridge.rocks instead of using the issue tracker.

## Credits

- [Jordan Partridge](https://github.com/jordanpartridge)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## About the Author

This package is maintained by Jordan Partridge. Visit [jordanpartridge.us](https://jordanpartridge.us) to learn more about the author and other projects.
