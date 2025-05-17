# Laravel Strava Client

A thoughtfully crafted Strava API integration for Laravel developers who value clean, maintainable code. Stop wrestling with OAuth and start building features that matter.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jordanpartridge/strava-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jordanpartridge/strava-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jordanpartridge/strava-client.svg?style=flat-square)](https://packagist.org/packages/jordanpartridge/strava-client)

This package reflects my belief that developer tools should be both powerful and pleasant to use. Built with Laravel best practices and a focus on developer experience.

## ✨ Why Choose This Package?

- 🔌 **True Plug & Play**: OAuth flow that just works, because you shouldn't need a degree in authentication
- 🔄 **Intelligent Token Handling**: Automatic refresh handling - set it and forget it
- 🏃‍♂️ **Activity Ready**: Fetch athlete activities with eloquent simplicity
- 🔒 **Security First**: Encrypted token storage and robust error handling out of the box
- 🎯 **Laravel Native**: Built the way Laravel packages should be
- 📦 **Production Ready**: Scales from personal projects to multi-user applications

## 🚀 Quick Start

### 1. Install the package

```bash
composer require jordanpartridge/strava-client
```

### 2. Run the installer

```bash
php artisan strava-client:install
```

### 3. Set up your User model

Add the `HasStravaTokens` trait and `HasStravaToken` interface to your User model:

```php
use JordanPartridge\StravaClient\Contracts\HasStravaToken;
use JordanPartridge\StravaClient\Concerns\HasStravaTokens;

class User extends Authenticatable implements HasStravaToken
{
    use HasStravaTokens;
}
```

### 4. Add your Strava credentials to `.env`

```env
STRAVA_CLIENT_ID=your-client-id
STRAVA_CLIENT_SECRET=your-client-secret
```

### 5. Connect to Strava

Visit `/strava/authorize` as an authenticated user to start the OAuth flow.

### 6. Start building!

```php
use JordanPartridge\StravaClient\Facades\StravaClient;

// Clean, intuitive API for fetching activities
$activities = StravaClient::activityForAthlete(page: 1, per_page: 10);

// Direct access to specific activities
$activity = StravaClient::getActivity($activityId);
```

## 🔄 Behind the Scenes

The package handles all the complex OAuth interactions so you don't have to:

1. Secure authorization initiation via `/strava/authorize`
2. Automated OAuth callback processing
3. Encrypted token storage
4. Automatic token refresh handling
5. Clean user token associations

## 🔧 Configuration

Fine-tune the package behavior through `config/strava-client.php`:

```php
return [
    // Customize the Strava access scope
    'scope' => env('STRAVA_CLIENT_SCOPE', 'read,activity:read_all'),
    
    // Adjust token refresh behavior
    'max_refresh_attempts' => env('STRAVA_CLIENT_MAX_REFRESH_ATTEMPTS', 3),
    
    // Set your post-connection redirect
    'redirect_after_connect' => env('STRAVA_CLIENT_REDIRECT_AFTER_CONNECT', '/admin'),
];
```

## ⚡️ Professional Error Handling

Handle API interactions with confidence using custom exception types:

```php
use JordanPartridge\StravaClient\Exceptions\Request\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\Request\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\Request\ResourceNotFoundException;

try {
    $activity = StravaClient::getActivity($id);
} catch (BadRequestException $e) {
    // Handle malformed requests
} catch (RateLimitExceededException $e) {
    // Handle API rate limits
} catch (ResourceNotFoundException $e) {
    // Handle missing activities
}
```

## 🧪 Quality Assurance

Run the test suite:

```bash
composer test
```

## 🔄 Compatibility

This package supports:
- PHP 8.2 or higher
- Laravel 10.x, 11.x, and 12.x

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👤 About the Author

Hi! I'm Jordan Partridge, and I build packages like this with a focus on developer experience and clean, maintainable code. If you appreciate the attention to detail in this package and my approach to solving problems, I'm available for hire on Laravel and PHP projects.

**[Visit my website](https://jordanpartridge.us) to learn more about my work and how we might collaborate on your next project.**

Need help with this package or want to discuss a project? Reach me at [jordan@partridge.rocks](mailto:jordan@partridge.rocks).

## ❤️ Support

If you find this package helpful, please consider:
- Starring the repo on GitHub
- Sharing it with other developers
- Checking out [my other packages](https://jordanpartridge.us)

---
Made with ♥️ in Laravel by a developer who cares about your experience
