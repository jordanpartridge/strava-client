# Strava Client for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)

This package provides a convenient way to interact with the Strava API in your Laravel application. It was born out of a passion for cycling and APIs, initially built into a personal website and now available as a standalone package.

## Features

- Easy integration with Strava API
- Laravel-friendly configuration
- Customizable and extendable

## Installation

You can install the package via composer:

```bash
composer require jordanpartridge/strava-client
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="strava-client-config"
```

Optionally, you can publish and run the migrations:

```bash
php artisan vendor:publish --tag="strava-client-migrations"
php artisan migrate
```


## Usage

Here's a basic example of how to use the Strava Client:

```php
use JordanPartridge\StravaClient\StravaClient;

$stravaClient = new StravaClient();
$activities = $stravaClient->activityForAthlete(page: 1);
```

If you would prefer to use the facade that's also available:

```php
use JordanPartridge\StravaClient\Facades\StravaClient;
$activities = StravaClient::activitiesForAthlete(page: 1);

This documentation is a work in progress, please refer to Strava's API documentation for more information.
```
For more detailed usage instructions and examples, please refer to the [documentation](https://github.com/jordanpartridge/strava-client/wiki).

## Testing

Run the tests with:

```bash
composer test
```

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email jordan@partridge.rocks instead of using the issue tracker.

## Credits

- [Jordan Partridge](https://github.com/jordanpartridge)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## About the Author

This package is maintained by Jordan Partridge. Check out my personal website at [jordanpartridge.us](https://jordanpartridge.us) to see how I've used this package and other projects.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
