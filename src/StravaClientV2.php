<?php

namespace JordanPartridge\StravaClient;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Concerns\LegacyMethods;
use JordanPartridge\StravaClient\Concerns\ResourceMethods;
use JordanPartridge\StravaClient\Contracts\StravaClientInterface;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

/**
 * Modern Strava Client with Dual Interface Architecture
 *
 * Provides both legacy and resource-based APIs for maximum compatibility
 * during the transition period. This class will become the main StravaClient
 * in v1.0.0, with legacy methods removed.
 */
class StravaClientV2 implements StravaClientInterface
{
    use LegacyMethods;
    use ResourceMethods;

    public function __construct(
        private Connector $strava,
        private int $max_refresh_attempts = 3
    ) {
        if ($this->max_refresh_attempts < 1) {
            throw new InvalidArgumentException('Max refresh attempts must be greater than 0.');
        }
    }

    /**
     * Exchange authorization code for tokens.
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array
    {
        $response = $this->strava->exchangeToken($code, $grant_type);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \RuntimeException('Failed to exchange token: '.$response->status(), $response->status());
    }

    /**
     * Set authentication tokens (legacy interface).
     */
    public function setToken(string $access_token, string $refresh_token): self
    {
        if (empty($access_token)) {
            throw new InvalidArgumentException('Access token cannot be empty.');
        }

        if (empty($refresh_token)) {
            throw new InvalidArgumentException('Refresh token cannot be empty.');
        }

        $this->strava->setToken($access_token, $refresh_token);

        return $this;
    }
}
