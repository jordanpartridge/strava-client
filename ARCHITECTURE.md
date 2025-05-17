# Technical Architecture

This document provides a detailed overview of the Laravel Strava Client package architecture, design patterns, and implementation details.

## Architectural Overview

The package follows a layered architecture with clear separation of concerns:

```
User Application
       │
       ▼
┌─────────────────┐
│  StravaClient   │  Service Layer (Business Logic)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Connector    │  HTTP Layer (API Communication)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Requests     │  Request Layer (API Endpoints)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Strava API     │  External Service
└─────────────────┘
```

### Design Patterns

The package utilizes several design patterns:

1. **Facade Pattern**: StravaClient facade provides a simple interface to the complex subsystem
2. **Adapter Pattern**: Connector adapts Saloon HTTP client for Strava API
3. **Repository Pattern**: StravaToken model handles token storage and retrieval
4. **Dependency Injection**: Components are injected rather than instantiated directly
5. **Gateway Pattern**: Isolates the application from the external Strava API

## Component Details

### StravaClient

The main service class that:
- Provides the primary API for developers
- Handles token refresh logic
- Manages error handling and exception mapping
- Validates input parameters

### Connector

The HTTP layer that:
- Handles communication with Strava API
- Manages authentication headers
- Handles token storage
- Creates and sends requests

### Request Classes

Individual request classes for different API endpoints:
- Encapsulate endpoint-specific logic
- Handle URL construction
- Set appropriate HTTP methods
- Validate endpoint-specific parameters

### Models and Storage

- **StravaToken**: Eloquent model for token persistence
- Uses Laravel's encryption for secure token storage
- Relates tokens to users via Eloquent relationships

## Authentication Flow

1. **Initialize Authorization**:
   - User visits `/strava/authorize`
   - RedirectController creates OAuth URL with configured parameters
   - User is redirected to Strava for authorization

2. **Handle Callback**:
   - Strava redirects back to `/strava/callback` with authorization code
   - CallBackController receives the code
   - Code is exchanged for access and refresh tokens
   - Tokens are stored in the database with the user relationship

3. **Token Management**:
   - Access tokens expire after 6 hours
   - When a token expires, the package automatically:
     - Detects the 401 Unauthorized response
     - Uses the refresh token to obtain a new access token
     - Updates the stored tokens
     - Retries the original request

## Error Handling Strategy

The package implements comprehensive error handling:

1. **Custom Exceptions**:
   - `BadRequestException`: 400 errors (malformed requests)
   - `ResourceNotFoundException`: 404 errors (activity not found)
   - `RateLimitExceededException`: 429 errors (too many requests)
   - `StravaServiceException`: 5xx errors (Strava server issues)
   - `MaxAttemptsException`: When token refresh fails repeatedly

2. **Exception Handling**:
   - HTTP status codes are mapped to appropriate exceptions
   - Exceptions contain the original response for detailed debugging
   - Consistent error interface for application developers

## Security Considerations

1. **Token Security**:
   - Access and refresh tokens are encrypted at rest
   - HTTPS is enforced for all API communication
   - Token refresh happens server-side

2. **Input Validation**:
   - All user input is validated before use
   - Parameters are type-checked and bounds-checked

## Performance Optimizations

1. **Token Refresh**:
   - Tokens are only refreshed when necessary
   - Refresh attempts are limited to prevent infinite loops

2. **Database Efficiency**:
   - Minimal schema with appropriate indexes
   - Database operations are transactional where appropriate

## Testing Strategy

1. **Unit Tests**:
   - Test individual components in isolation
   - Mock external dependencies

2. **Integration Tests**:
   - Test the interaction between components
   - Use in-memory database for token storage tests

3. **Architecture Tests**:
   - Enforce architectural constraints
   - Verify dependency directions

## Extension Points

The package is designed to be extensible:

1. **Custom Strava API endpoints**:
   - Add new request classes in the Requests namespace
   - Add corresponding methods to the Connector
   - Expose via StravaClient with appropriate error handling

2. **Custom token storage**:
   - Extend or replace the StravaToken model
   - Customize the token relationship