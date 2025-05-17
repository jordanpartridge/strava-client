# Security Policy

## Supported Versions

Currently supported versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 0.2.x   | :white_check_mark: |
| 0.1.x   | :x:                |

## Reporting a Vulnerability

The Laravel Strava Client package takes security issues seriously. We appreciate your efforts to responsibly disclose your findings.

To report a security vulnerability, please email [jordan@partridge.rocks](mailto:jordan@partridge.rocks) with a description of the issue, the steps you took to create the issue, affected versions, and, if known, mitigations for the issue.

Once you've reported a security vulnerability, the maintainer will:

1. Acknowledge receipt of your vulnerability report
2. Work with you to understand and validate the issue
3. Develop and implement a fix for the issue
4. Release a security update for the affected versions
5. Publicly acknowledge the security issue and your contribution (if desired)

## Security Best Practices

When using Laravel Strava Client, we recommend following these security best practices:

### Environment Variables

Always store your Strava API credentials in environment variables, never in version control:

```env
STRAVA_CLIENT_ID=your-client-id
STRAVA_CLIENT_SECRET=your-client-secret
```

### Access Control

Ensure that your OAuth callback route is properly protected and only accessible by authenticated users who should have Strava connection permissions.

### Token Storage

The package automatically encrypts OAuth tokens before storing them in the database. Do not modify this behavior unless you implement an equivalent security measure.

### HTTPS

Always use HTTPS in production environments to protect API communications and OAuth flows.

## Security Features

Laravel Strava Client includes several security features:

1. **Token Encryption**: All tokens are encrypted at rest using Laravel's encryption facilities
2. **Input Validation**: All user input is validated before use
3. **Automatic Token Refresh**: Expired tokens are automatically refreshed securely
4. **Exception Handling**: Security-related exceptions are handled gracefully