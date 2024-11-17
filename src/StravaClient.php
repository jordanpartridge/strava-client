<?php

namespace JordanPartridge\StravaClient;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Exceptions\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\RefreshTokenException;
use JordanPartridge\StravaClient\Exceptions\ResourceNotFoundException;
use JordanPartridge\StravaClient\Exceptions\StravaServiceException;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

final readonly class StravaClient
{
    public function __construct(private Connector $strava, private int $max_refresh_attempts = 1)
    {
        if ($this->max_refresh_attempts < 1) {
            throw new InvalidArgumentException('Max refresh attempts must be greater than 0.');
        }
    }

    /**
     * Exchange authorization code for tokens
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array
    {
        return $this->handleRequest(
            fn () => $this->strava->exchangeToken($code, $grant_type)
        );
    }

    /**
     * Set access and refresh tokens
     */
    public function setToken(string $access_token, string $refresh_token): void
    {
        if ($this->shouldSetToken($access_token, $refresh_token)) {
            $this->strava->setToken($access_token, $refresh_token);
        }

        throw new InvalidArgumentException('Access and refresh tokens must be set');
    }

    private function shouldSetToken(string $access_token, string $refresh_token): bool
    {
        return ! empty($access_token) && ! empty($refresh_token);
    }

    /**
     * Get activities for authenticated athlete
     *
     * @throws RequestException|FatalRequestException|JsonException
     */
    public function activityForAthlete(int $page, int $per_page): array
    {
        return $page < 1 || $per_page < 1
            ? throw new InvalidArgumentException('Page and per_page must be positive integers')
            : $this->handleRequest(fn () => $this->strava->activityForAthlete([
                'page' => $page,
                'per_page' => $per_page,
            ]));
    }

    /**
     * Get a specific activity by ID
     *
     *
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RateLimitExceededException
     * @throws RefreshTokenException
     * @throws RequestException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    public function getActivity(int $id): array
    {
        return $id < 1
            ?
            throw new InvalidArgumentException('Activity ID must be positive')
            :
            $this->handleRequest(fn () => $this->strava->getActivity($id));
    }

    /**
     * Handle API request with automatic token refresh
     *
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RateLimitExceededException
     * @throws RefreshTokenException
     * @throws RequestException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    private function handleRequest(callable $request, int $attempts = 0): array
    {
        if ($attempts >= $this->max_refresh_attempts) {
            throw new RefreshTokenException($request(), 'Maximum retry attempts exceeded');
        }
        $response = $request();

        if (! $response->failed()) {
            return $response->json();
        }

        return match ($response->status()) {
            401 => $this->handleUnauthorized($request, $attempts + 1, $response),
            404 => throw new ResourceNotFoundException($response),
            400 => throw new BadRequestException($response),
            429 => throw new RateLimitExceededException($response),
            500, 502, 503, 504 => throw new StravaServiceException($response),
            default => throw new RequestException($response),
        };
    }

    /**
     * Handle unauthorized response by refreshing token and retrying
     *
     *
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RefreshTokenException
     * @throws RequestException
     */
    private function handleUnauthorized(callable $request, int $attempts = 0, $failed_response = null): array
    {
        if ($attempts >= $this->max_refresh_attempts) {
            throw new RefreshTokenException($failed_response, 'Maximum token refresh attempts exceeded');
        }
        $response = $this->handleRequest(fn () => $this->strava->refreshToken());

        // Update tokens after successful refresh
        $this->strava->setToken(
            $response['access_token'],
            $response['refresh_token']
        );

        return $this->handleRequest($request, $attempts + 1);
    }
}
