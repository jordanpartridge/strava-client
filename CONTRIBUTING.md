# Contributing to Laravel Strava Client

Thank you for considering contributing to the Laravel Strava Client package! This document outlines the guidelines and processes for contributing.

## Code of Conduct

Please be respectful and considerate of others when contributing to this project. We aim to foster an inclusive and welcoming environment for everyone.

## Development Environment Setup

1. **Fork and clone the repository**:
   ```bash
   git clone https://github.com/your-username/strava-client.git
   cd strava-client
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up for testing**:
   ```bash
   composer prepare
   ```

## Development Workflow

1. **Create a feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes and commit them**:
   - Follow the existing code style
   - Include tests for your changes
   - Ensure all tests pass

3. **Run tests and static analysis**:
   ```bash
   composer test
   composer analyse
   ```

4. **Format your code**:
   ```bash
   composer format
   ```

5. **Push your changes**:
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a pull request**:
   - Submit to the `main` branch of the original repository
   - Provide a clear description of the changes
   - Reference any related issues

## Pull Request Guidelines

- **One feature per pull request**: Keep PRs focused on a single change
- **Update documentation**: Ensure README and docblocks are updated
- **Add tests**: Include tests for new functionality
- **Maintain compatibility**: Ensure changes work with supported Laravel versions
- **Follow coding standards**: Adhere to PSR-12 and existing code style

## Testing

The package uses Pest PHP for testing. All tests should pass before submitting a PR:

```bash
composer test
```

For a specific test:

```bash
composer test -- --filter=TestName
```

## Code Style

We use Laravel Pint for code formatting:

```bash
composer format
```

And PHPStan for static analysis:

```bash
composer analyse
```

## Documentation

- Update the README.md if you're adding/changing features
- Add PHPDoc blocks to all classes and methods
- Include examples where appropriate

## Version Control Guidelines

- Use descriptive commit messages
- Reference issue numbers in commit messages when applicable
- Keep commits focused on single logical changes

## Release Process

1. Update CHANGELOG.md with new version
2. Tag release in GitHub
3. Packagist will automatically update

## Questions?

If you have any questions about contributing, please open an issue or contact the maintainer directly.