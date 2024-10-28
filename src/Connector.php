<?php

namespace JordanPartridge\StravaClient;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Requests\ActivityRequest;
use JordanPartridge\StravaClient\Requests\AthleteActivityRequest;
use JordanPartridge\StravaClient\Requests\TokenExchange;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector as BaseConnector;
use Saloon\Http\Response;

class Connector extends BaseConnector
{
    /**
     * The access token for the connector.
     */
    private ?string $access_token = null;

    private string $refresh_token;

    /**
     * Set the token for the connector.
     */
    public function setToken(string $access_token, string $refresh_token): Connector
    {
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getActivity(int $id): Response
    {
        return $this->send(new ActivityRequest($id));
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
     * Since I want this class to be the only one maintaining the token, and I don't want to expose the token
     * let's give other classes an easy way to refresh the token, now that im thinking about this,
     * This could potentially be the wrong approach since I just said I want this class to be the only one maintaining
     * the token, but let's iterate and see if this is the right approach.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function refreshToken(): Response
    {
        return $this->exchangeToken($this->refresh_token, 'refresh_token');
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
     * @param  string  $code  The authorization code or refresh token depending on grant type
     * @param  string  $grant_type  Must be either 'authorization_code' or 'refresh_token'
     * @return Response The Saloon Response object containing the token data
     *
     * @throws InvalidArgumentException When an invalid grant type is provided
     * @throws RequestException When the API request fails
     * @throws FatalRequestException When a critical request error occurs
     *
     * @link https://developers.strava.com/docs/getting-started/ Strava API Documentation
     */
    public function exchangeToken(string $code, string $grant_type): Response
    {
        $allowed_grant_types = ['authorization_code', 'refresh_token'];

        if (! in_array($grant_type, $allowed_grant_types)) {
            throw new InvalidArgumentException('Invalid grant type provided.');
        }

        return $this->send(new TokenExchange($code, $grant_type));
    }

    /**
     * The base URL for the API abstracted to the config to allow for easy overriding.
     */
    public function resolveBaseUrl(): string
    {
        return config('strava-client.base_url');
    }

    /**
     * Default header setup for json.
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * The default authentication method for the connector.
     */
    protected function defaultAuth(): ?TokenAuthenticator
    {
        return $this->access_token ? new TokenAuthenticator($this->access_token) : null;
    }
}
