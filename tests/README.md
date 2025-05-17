# Tests Documentation

This directory contains tests for the Laravel Strava Client package. The package uses Pest PHP, a testing framework with a focus on simplicity and readability.

## Test Files

- **ActivityRequestTest.php**: Tests for the Activity request functionality.
- **ArchTest.php**: Architecture tests that ensure the codebase follows good practices.
- **Pest.php**: Configuration file for Pest PHP.
- **StravaTest.php**: Tests for core Strava client functionality.
- **TestCase.php**: Base test case class that sets up the testing environment.

## Running Tests

To run the tests:

```bash
composer test
```

For test coverage:

```bash
composer test-coverage
```

## Test Approach

The tests follow these principles:

1. **Unit Tests**: Testing individual components in isolation.
2. **Feature Tests**: Testing the integration between components.
3. **Architecture Tests**: Ensuring the codebase follows best practices.

## Writing New Tests

When adding new features, please ensure:

1. Test both the happy path and error cases
2. Mock external dependencies (especially Strava API calls)
3. Follow the existing test style using Pest's expressive syntax

## CI/CD Integration

Tests are automatically run on GitHub Actions for:
- Multiple PHP versions (8.2, 8.3)
- Multiple Laravel versions (10.x, 11.x, 12.x)
- Different dependency sets (prefer-lowest and prefer-stable)
- Both Linux and Windows environments

This ensures maximum compatibility and stability of the package.