# Technical Debt and Future Enhancements

This document tracks known technical debt and opportunities for future enhancement in the Laravel Strava Client package.

## Current Technical Debt

### 1. API Coverage
- **Description**: The package currently only covers a limited subset of the Strava API (activities).
- **Impact**: Users need to implement their own solutions for other Strava API endpoints.
- **Resolution Plan**: Incrementally add support for additional Strava API endpoints.

### 2. Error Handling Refinement
- **Description**: While custom exceptions exist, error messages could be more descriptive and provide more context.
- **Impact**: Developers may spend extra time debugging API errors.
- **Resolution Plan**: Enhance error messages and provide more contextual information in exceptions.

### 3. Test Coverage
- **Description**: Some edge cases and error scenarios could benefit from more thorough testing.
- **Impact**: Potential for uncaught bugs in specific situations.
- **Resolution Plan**: Expand test suite with more edge cases and error scenarios.

## Future Enhancements

### 1. Expanded API Support
- **Description**: Add support for more Strava API endpoints:
  - Segments
  - Clubs
  - Routes
  - Stream data
  - Upload functionality
- **Priority**: Medium
- **Complexity**: Medium

### 2. Webhook Support
- **Description**: Implement support for Strava webhooks to receive real-time updates.
- **Priority**: High
- **Complexity**: High

### 3. Caching Layer
- **Description**: Implement intelligent caching for Strava API responses to improve performance and reduce API calls.
- **Priority**: Medium
- **Complexity**: Medium

### 4. Batch Operations
- **Description**: Support for batch operations to fetch multiple resources in a single request.
- **Priority**: Low
- **Complexity**: Medium

### 5. Rate Limiting Management
- **Description**: Implement advanced rate limiting management to prevent hitting Strava API limits.
- **Priority**: Medium
- **Complexity**: Medium

### 6. Activity Sync Command
- **Description**: Add an Artisan command to sync activities from Strava to a local database.
- **Priority**: Low
- **Complexity**: Medium

### 7. Event System
- **Description**: Implement Laravel events for key operations (connection, token refresh, activity fetch, etc.).
- **Priority**: Medium
- **Complexity**: Low

### 8. Enhanced Error Reporting
- **Description**: More detailed error reporting with suggestions for resolution.
- **Priority**: Low
- **Complexity**: Low

### 9. Admin UI
- **Description**: Add an optional admin UI for managing Strava connections and viewing status.
- **Priority**: Low
- **Complexity**: High

### 10. Token Export/Import
- **Description**: Add functionality to export and import tokens for deployment across environments.
- **Priority**: Low
- **Complexity**: Medium

## Deprecation Plans

Currently, there are no planned deprecations. The package follows semantic versioning:

- Major version changes may include breaking changes
- Minor version changes add functionality in a backward-compatible manner
- Patch version changes include backward-compatible bug fixes

## Decision Records

### Decision: Use Saloon PHP for API Requests
- **Context**: Needed a clean, maintainable way to interact with Strava API
- **Decision**: Chose Saloon PHP over Guzzle or direct cURL
- **Rationale**: Saloon provides a structured approach to API clients with built-in features for authentication, request/response handling, and testing
- **Consequences**: Introduces a dependency but significantly improves code organization and maintainability

### Decision: Store Tokens in Dedicated Table
- **Context**: Need to securely store OAuth tokens
- **Decision**: Created a dedicated `strava_tokens` table rather than adding columns to the users table
- **Rationale**: Allows for clean separation of concerns and avoids modifying the application's users table
- **Consequences**: Requires an extra join when retrieving token data but provides better organization and flexibility