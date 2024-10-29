<?php

namespace JordanPartridge\StravaClient;

use Exception;
use JordanPartridge\StravaClient\Exceptions\ResourceNotFoundException;
use Saloon\Http\Response;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

final class StravaClient
{
    public function __construct(private readonly Connector $strava)
    {
    }

    /**
     * Exchange authorization code for tokens
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
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
     * @throws Exception
     */
    public function activityForAthlete(int $page, int $per_page): array
    {
        return $this->handleRequest(function () use ($page, $per_page): Response {
            return $this->strava->activityForAthlete([
                'page' => $page,
                'per_page' => $per_page
            ]);
        });
    }

    /**
     * Get a specific activity by ID
     *
     * @throws Exception
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
     * @throws Exception
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
            400 => throw new Exception($response->json('message') ?? 'Bad request'),
            429 => throw new Exception('Rate limit exceeded'),
            500, 502, 503, 504 => throw new Exception('Strava API service error'),
            default => throw new Exception('Unknown error occurred'),
        };
    }

    /**
     * Handle unauthorized response by refreshing token and retrying
     *
     * @throws Exception
     */
    private function handleUnauthorized(callable $request): array
    {
        $refresh = $this->strava->refreshToken();

        if ($refresh->failed()) {
            throw new Exception('Token refresh failed: ' . ($refresh->json('message') ?? 'Unknown error'));
        }

        // Update tokens after successful refresh
        $this->strava->setToken(
            $refresh->json('access_token'),
            $refresh->json('refresh_token')
        );

        // Retry original request with new token
        $response = $request();

        if ($response->failed()) {
            throw new Exception('Request failed after token refresh');
        }

        return $response->json();
    }
}
