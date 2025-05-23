# Laravel 12 Support Plan

## Overview

This document outlines the plan to add full Laravel 12 support to the Strava Client package. Based on recent commits, there has been active work on Laravel 12 compatibility, but additional steps are needed for full support.

## Current Status

### ✅ Completed
- Initial Laravel 12 compatibility workflow setup
- Updated testing libraries for Laravel 12 support
- Pest plugins updated for Laravel 12 compatibility
- CI/CD pipeline modified to test against Laravel 12 (with allowed failures)

### 🚧 In Progress
- Composer.json constraints currently limited to Laravel 10.x and 11.x
- Full test suite passing with Laravel 12

## Required Changes

### 1. Update Composer Dependencies
```json
"require": {
    "php": "^8.2",
    "illuminate/contracts": "^10.0||^11.0||^12.0",
    "saloonphp/saloon": "^3.0",
    "spatie/laravel-package-tools": "^1.16"
}
```

### 2. Update Development Dependencies
- Verify all dev dependencies support Laravel 12:
  - `orchestra/testbench`: Need to update to `^9.0` for Laravel 12
  - `larastan/larastan`: Check for Laravel 12 compatibility
  - All Pest plugins: Already updated in recent commits

### 3. GitHub Actions Workflow Updates
Add Laravel 12 to the test matrix in `.github/workflows/run-tests.yml`:
```yaml
matrix:
  laravel: [12.*, 11.*, 10.*]
  include:
    - laravel: 12.*
      testbench: 9.*
      carbon: ^3.0
```

### 4. Code Compatibility Checks
- Review any deprecated features that might be removed in Laravel 12
- Check for breaking changes in:
  - Authentication system
  - Database/Eloquent changes
  - Service provider registration
  - Route handling

### 5. Testing Requirements
- Ensure all tests pass with Laravel 12
- Add specific Laravel 12 feature tests if needed
- Update PHPStan configuration for Laravel 12 types

## Implementation Timeline

### Phase 1: Dependency Updates (Immediate)
1. Update composer.json constraints
2. Test locally with Laravel 12
3. Fix any immediate compatibility issues

### Phase 2: CI/CD Updates (Week 1)
1. Update GitHub Actions workflows
2. Ensure tests pass in CI environment
3. Remove "allowed failures" for Laravel 12

### Phase 3: Documentation (Week 2)
1. Update README with Laravel 12 support
2. Add migration guide if breaking changes exist
3. Update CHANGELOG with version details

### Phase 4: Release (Week 3)
1. Tag new version with Laravel 12 support
2. Publish release notes
3. Update Packagist

## Potential Breaking Changes

Based on Laravel 12 early access documentation:
- Minimum PHP version might increase to 8.3
- Some legacy authentication methods may be removed
- Database connection handling improvements

## Testing Strategy

1. **Unit Tests**: Ensure all existing tests pass
2. **Integration Tests**: Test OAuth flow with Laravel 12
3. **Compatibility Tests**: Test against all supported Laravel versions
4. **Performance Tests**: Verify no performance regressions

## Risk Mitigation

- Maintain backward compatibility with Laravel 10.x and 11.x
- Use feature detection rather than version checks where possible
- Provide clear upgrade documentation
- Consider a major version bump if breaking changes are required

## Success Criteria

- [ ] All tests pass with Laravel 12
- [ ] No breaking changes for existing users
- [ ] Documentation updated
- [ ] CI/CD pipeline fully supports Laravel 12
- [ ] Package published with Laravel 12 support