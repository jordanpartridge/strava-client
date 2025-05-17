# Database Documentation

This directory contains database-related files for the Laravel Strava Client package.

## Directory Structure

### Factories
Factory definitions for creating model instances in tests.
- **ModelFactory.php**: Factory for creating StravaToken model instances.

### Migrations
Database migrations for setting up the necessary tables.
- **create_strava_tokens_table.php.stub**: Migration to create the strava_tokens table.

## Strava Tokens Table

The `strava_tokens` table is created by the migration and stores:

1. `id`: Primary key
2. `user_id`: Foreign key reference to the users table
3. `access_token`: Encrypted Strava API access token
4. `refresh_token`: Encrypted Strava API refresh token
5. `expires_at`: Timestamp when the access token expires
6. `athlete_id`: Strava athlete ID associated with the tokens
7. `created_at` and `updated_at`: Standard Laravel timestamps

## Token Security

Security is a priority for this package:
- Access and refresh tokens are automatically encrypted when stored in the database
- The package uses Laravel's built-in encryption mechanism
- No plain text tokens are ever stored or logged

## Installation

The migration will be published to your application when you run:

```bash
php artisan strava-client:install
```

You can then run the migration:

```bash
php artisan migrate
```

## Model Relationships

The package uses Eloquent relationships to associate tokens with users:
- Each User can have one StravaToken
- This is achieved through the `HasStravaTokens` trait that should be added to your User model