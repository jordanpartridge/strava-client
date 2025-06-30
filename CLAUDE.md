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

# Run tests with coverage
composer test-coverage

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

# Run a specific test
vendor/bin/pest tests/Unit/StravaClientTest.php

# Run tests in a specific directory
vendor/bin/pest tests/Feature/

# Run architecture tests
vendor/bin/pest --filter="arch"
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

Tests are written using Pest PHP and organized in a structured hierarchy:

1. **Unit Tests** (`tests/Unit/`): Individual component testing
   - Models, concerns, requests, and service classes
   - HTTP controllers with mocked dependencies
2. **Feature Tests** (`tests/Feature/`): Full integration testing
   - Complete OAuth flow testing
   - End-to-end API interaction scenarios
3. **Architecture Tests** (`tests/ArchTest.php`): Code quality enforcement
   - Ensures proper class organization and dependencies
4. **HTTP Mock Tests**: Using Saloon's testing helpers for API simulation

## Workbench Development

The package uses Laravel's Testbench Workbench for development:

- **Workbench App** (`workbench/app/`): Simulated Laravel application for testing
- **Database Migrations** (`tests/database/migrations/`): Test-specific database schema
- **Service Provider** automatically loaded for testing environment

## Code Style

- PSR-12 compliance enforced via Laravel Pint
- Static analysis via Larastan/PHPStan (level 8)
- DocBlocks for all public methods
- Type hints for parameters and return types

## Package Structure

- **Commands**: Artisan commands for package installation and management
- **HTTP Layer**: Controllers for OAuth flow (`RedirectController`, `CallBackController`)
- **Requests**: Saloon request classes for different API endpoints
- **Models**: Eloquent models with encrypted token storage
- **Exceptions**: Custom exception hierarchy for different error scenarios

## Development Tools & MCP Usage

### GitHub Operations
Use GitHub CLI for all GitHub-related operations until Conduit becomes more stable and feature-rich:

```bash
# View repository status and issues
gh repo view --web
gh issue list
gh pr list --state open

# Create and manage pull requests
gh pr create --title "Add new feature" --body "Description of changes"
gh pr view 123
gh pr checkout 123
gh pr merge 123

# Work with releases
gh release list
gh release create v1.0.0 --title "Release v1.0.0" --notes "Release notes"

# Repository insights
gh api repos/:owner/:repo/pulls/123/files
gh api repos/:owner/:repo/actions/runs
```

### Efficient MCP Patterns

1. **Batch Operations**: When working with multiple files or operations, batch tool calls together for optimal performance
2. **GitHub CLI over Web**: Prefer `gh` commands for repository operations, issue management, and CI/CD insights
3. **JSON Processing**: Use `gh` with `--json` flag and `jq` for structured data processing:
   ```bash
   gh pr list --json number,title,state --jq '.[] | select(.state=="OPEN")'
   ```

### Development Workflow Integration

```bash
# Quick development cycle
composer test && composer analyse && composer format

# Pre-commit workflow
composer test && gh pr create
# Use CI wait time for repository maintenance
gh issue list --state open  # Review open issues
gh pr list --state open     # Check other PRs needing attention
gh pr checkout <pr-number> && git rebase main  # Rebase stale PRs if needed
# Close completed issues, review stale PRs, update documentation
gh pr checks && gh pr merge  # Verify checks pass before merging

# Release workflow
composer test-coverage  # Ensure coverage before release
gh release create --generate-notes
```