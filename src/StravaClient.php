<?php

namespace JordanPartridge\StravaClient;

use JordanPartridge\StravaClient\Exceptions\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\RefreshTokenException;
use JordanPartridge\StravaClient\Exceptions\ResourceNotFoundException;
use JordanPartridge\StravaClient\Exceptions\StravaServiceException;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use Saloon\Http\Response;

final readonly class StravaClient
{
    public function __construct(private Connector $strava) {}

    /**
     * Exchange authorization code for tokens
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array
    {
        return $this->strava->exchangeToken($code, $grant_type)->json();
    }

    /**
     * Set access and refresh tokens
     */
    public function setToken(string $access_token, string $refresh_token): void
    {
        $this->strava->setToken($access_token, $refresh_token);
    }

    /**
     * Get activities for authenticated athlete
     *
     * @throws RequestException|FatalRequestException|JsonException
     */
    public function activityForAthlete(int $page, int $per_page): array
    {
        return $this->handleRequest(function () use ($page, $per_page): Response {
            return $this->strava->activityForAthlete([
                'page' => $page,
                'per_page' => $per_page,
            ]);
        });
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
        return $this->handleRequest(function () use ($id): Response {
            return $this->strava->getActivity($id);
        });
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
    private function handleRequest(callable $request): array
    {
        $response = $request();

        if (! $response->failed()) {
            return $response->json();
        }

        return match ($response->status()) {
            401 => $this->handleUnauthorized($request),
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
    private function handleUnauthorized(callable $request): array
    {
        $response = $this->strava->refreshToken();

        if ($response->failed()) {
            throw new RefreshTokenException($response);
        }

        // Update tokens after successful refresh
        $this->strava->setToken(
            $response->json('access_token'),
            $response->json('refresh_token')
        );

        // Retry original request with new token
        $response = $request();

        if ($response->failed()) {
            throw new RefreshTokenException($response);
        }

        return $response->json();
    }
}
