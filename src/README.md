# Source Code Documentation

This directory contains all the source code for the Laravel Strava Client package. Below is a description of each component and how they work together.

## Core Components

- **StravaClient.php**: Main entry point for the package. Handles the business logic, token refresh, and error handling.
- **Connector.php**: HTTP client wrapper using Saloon PHP for making actual API calls to Strava.
- **StravaClientServiceProvider.php**: Laravel service provider that bootstraps the package within a Laravel application.

## Directory Structure

### Commands
Contains artisan command(s) for the package.
- **StravaClientCommand.php**: Install command that sets up the necessary configuration.

### Concerns
Traits that can be used by models in your application.
- **HasStravaTokens.php**: Trait to be used on User models to add Strava token relationship functionality.

### Contracts
Interfaces that define the behavior of package components.
- **HasStravaToken.php**: Interface that User models should implement to work with the package.

### Exceptions
Custom exception classes for better error handling.
- **Authentication/MaxAttemptsException.php**: Thrown when maximum token refresh attempts is exceeded.
- **Request/BadRequestException.php**: Thrown for 400 errors from Strava API.
- **Request/RateLimitExceededException.php**: Thrown when Strava's rate limit is exceeded.
- **Request/ResourceNotFoundException.php**: Thrown for 404 errors from Strava API.
- **Request/StravaServiceException.php**: Thrown for server errors (5xx) from Strava API.

### Facades
Laravel facades for easier access to package functionality.
- **StravaClient.php**: Facade for the StravaClient class.

### Http/Controllers
Controllers for handling OAuth flow.
- **CallBackController.php**: Handles the OAuth callback from Strava.
- **RedirectController.php**: Initiates the OAuth flow by redirecting to Strava.

### Models
Eloquent models for data storage.
- **StravaToken.php**: Model for storing and managing Strava OAuth tokens.

### Requests
Saloon request objects for different Strava API endpoints.
- **ActivityRequest.php**: Request for fetching a specific activity.
- **AthleteActivityRequest.php**: Request for fetching athlete activities.
- **TokenExchange.php**: Request for OAuth token operations.

## Architecture

The package follows a clean, layered architecture:

1. **Service Layer**: `StravaClient` class acts as the main entry point.
2. **HTTP Layer**: `Connector` class manages the actual HTTP requests.
3. **Request Layer**: Individual request classes for different API endpoints.
4. **Storage Layer**: `StravaToken` model for persistence of OAuth tokens.

## Flow

1. User connects to Strava via `/strava/authorize` route
2. Strava redirects back to the callback URL
3. Token is exchanged and stored in the database
4. StravaClient automatically handles token refresh when needed
5. Application can make Strava API calls via the StravaClient facade