# Strava Client integration for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)

This package started with my desire to play with Strava's API, my love for biking and API's in general. I built most of
the functionality directly into my site at first but I decided the first test for a package would be to make sure, I
can replicate my existing functionality. Want to see how I've used this package? Check out my site at [jordanpartridge.us](https://jordanpartridge.us).

## Installation

You can install the package via composer:

```bash
composer require jordanpartridge/strava-client
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="strava-client-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="strava-client-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="strava-client-views"
```

## Usage

```php
$stravaClient = new JordanPartridge\StravaClient();
echo $stravaClient->echoPhrase('Hello, JordanPartridge!');
```

## Testing

```bash
composer test
```

# Strava Client for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jordanpartridge/strava-client/run-tests?label=tests)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jordanpartridge/strava-client/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)

This package provides a convenient way to interact with the Strava API in your Laravel application. It was born out of a passion for cycling and APIs, initially built into a personal website and now available as a standalone package.

## Features

- Easy integration with Strava API
- Laravel-friendly configuration
- Customizable and extendable

## Installation

You can install the package via composer:


composer require jordanpartridge/strava-client


## Configuration

Publish the config file:


php artisan vendor:publish --tag="strava-client-config"


Optionally, you can publish and run the migrations:


php artisan vendor:publish --tag="strava-client-migrations"
php artisan migrate


If you want to customize the views, you can publish them as well:


php artisan vendor:publish --tag="strava-client-views"


## Usage

Here's a basic example of how to use the Strava Client:


use JordanPartridge\StravaClient\StravaClient;

$stravaClient = new StravaClient();
$activities = $stravaClient->getActivities();


For more detailed usage instructions and examples, please refer to the [documentation](https://github.com/jordanpartridge/strava-client/wiki).

## Testing

Run the tests with:


composer test


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email jordan@example.com instead of using the issue tracker.

## Credits

- [Jordan Partridge](https://github.com/jordanpartridge)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## About the Author

This package is maintained by Jordan Partridge. Check out my personal website at [jordanpartridge.us](https://jordanpartridge.us) to see how I've used this package and other projects.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jordan Partridge](https://github.com/jordanpartridge)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
