# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Laravel Strava Client

This package provides a Laravel integration for the Strava API, offering OAuth authentication flow handling, token management, and simplified activity retrieval.

## Development Commands

```bash
# Install dependencies
composer install

# Run tests
composer test

# Static analysis
composer analyse

# Code formatting
composer format

# Start development server with the workbench
composer start

# Build the workbench
composer build

# Clear workbench
composer clear
```

## Core Architecture

The package follows a clean, layered architecture:

1. **Service Layer**: `StravaClient` class serves as the main entry point, handling token refresh logic and error handling.
2. **HTTP Layer**: `Connector` class (using Saloon PHP) manages actual HTTP requests to the Strava API.
3. **Request Layer**: Individual request classes for different API endpoints (e.g., `ActivityRequest`, `AthleteActivityRequest`).
4. **Storage Layer**: `StravaToken` model and `HasStravaTokens` trait for persistence of OAuth tokens.

## Key Components

### 1. Authentication Flow
- OAuth 2.0 implementation with automatic token refresh handling
- Token storage in encrypted database fields
- Maximum retry attempts configurable
- Custom exceptions for common error cases

### 2. Data Access
- Activity retrieval with pagination support
- Individual activity lookup by ID
- Clean method signatures with proper type hints
- Comprehensive error handling

### 3. Token Management
- Eloquent relationship between User and StravaToken
- Encrypted token storage
- Automatic refresh token handling

### 4. Exception Handling
- Custom exception classes for different error scenarios:
  - `BadRequestException`
  - `RateLimitExceededException`
  - `ResourceNotFoundException`
  - `StravaServiceException`
  - `MaxAttemptsException`

## Integration Strategy

When implementing changes:

1. Add new API endpoints as request classes in `src/Requests/`
2. Add corresponding method in `Connector` class
3. Expose functionality through `StravaClient` with proper error handling
4. Add tests for each new feature
5. Document any new config options

## Main Files to Know

- `src/StravaClient.php`: Primary service class and main entry point
- `src/Connector.php`: HTTP client wrapper using Saloon
- `src/Models/StravaToken.php`: Eloquent model for token storage
- `src/Concerns/HasStravaTokens.php`: Trait for user model integration
- `src/StravaClientServiceProvider.php`: Laravel service provider
- `config/strava-client.php`: Configuration file

## Testing Approach

Tests are written using Pest PHP:

1. Unit tests for individual components
2. Integration tests for the full authentication flow
3. HTTP mock tests using Saloon testing helpers

## Code Style

- PSR-12 compliance enforced via Laravel Pint
- Static analysis via Larastan/PHPStan (level 8)
- DocBlocks for all public methods
- Type hints for parameters and return types