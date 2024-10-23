<?php

namespace JordanPartridge\StravaClient;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Requests\AthleteActivityRequest;
use JordanPartridge\StravaClient\Requests\TokenExchange;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector as BaseConnector;
use Saloon\Http\Response;

class Connector extends BaseConnector
{
    private ?string $token = null;


    /**
     * Set the bearer token once we have it, not sure if this will
     * eventually be handled internally but as of right now I'm trying
     * to make sure all the functionality gets handled.
     * @param string|null $token
     *
     * @return Connector
     */
    public function setToken(?string $token = null): Connector
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function activityForAthlete(array $payload): Response
    {
        return $this->send(new AthleteActivityRequest($payload));
    }


    /**
     * Exchange an authorization code for an access token or refresh an existing token.
     *
     * This method handles two OAuth 2.0 flows:
     * 1. Converting an authorization code to an access token (initial authentication)
     * 2. Using a refresh token to obtain a new access token (token refresh)
     *
     * Example using authorization code:
     * ```php
     * $response = $connector->exchangeToken(
     *     code: $authorizationCode,
     *     grant_type: 'authorization_code'
     * );
     * ```
     *
     * Example refreshing a token:
     * ```php
     * $response = $connector->exchangeToken(
     *     code: $refreshToken,
     *     grant_type: 'refresh_token'
     * );
     * ```
     *
     * The response will contain:
     * ```json
     * {
     *     "token_type": "Bearer",
     *     "access_token": "a1b2c3...",
     *     "refresh_token": "e5f6g7...",
     *     "expires_at": 1568775134,
     *     "scope": "read,activity:read"
     * }
     * ```
     *
     * @param string $code The authorization code or refresh token depending on grant type
     * @param string $grant_type Must be either 'authorization_code' or 'refresh_token'
     *
     * @throws InvalidArgumentException When an invalid grant type is provided
     * @throws RequestException When the API request fails
     * @throws FatalRequestException When a critical request error occurs
     *
     * @return Response The Saloon Response object containing the token data
     *
     * @link https://developers.strava.com/docs/getting-started/ Strava API Documentation
     */
    public function exchangeToken(string $code, string $grant_type): Response
    {
        $allowed_grant_types = ['authorization_code', 'refresh_token'];

        if (!in_array($grant_type, $allowed_grant_types)) {
            throw new InvalidArgumentException('Invalid grant type provided.');
        }

        return $this->send(new TokenExchange($code, $grant_type));
    }

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.strava.com/api/v3';
    }

    /**
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }

    protected function defaultAuth(): ?TokenAuthenticator
    {
        return $this->token ? new TokenAuthenticator($this->token) : null;
    }
}
