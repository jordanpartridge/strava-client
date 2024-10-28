<?php

namespace JordanPartridge\StravaClient;

use Exception;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

final class StravaClient
{
    public function __construct(private Connector $strava)
    {
        //
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array
    {
        return $this->strava->exchangeToken($code, $grant_type)->json();
    }

    public function setToken(string $access_token, string $refresh_token): void
    {
        $this->strava->setToken($access_token, $refresh_token);
    }

    public function activityForAthlete($page, $per_page): array
    {
        return $this->handleRequest(function () use ($page, $per_page) {
            return $this->strava->activityForAthlete(['page' => $page, 'per_page' => $per_page]);
        });
    }

    private function handleRequest(callable $request): array
    {
        $response = $request();

        if ($response->failed()) {
            return match ($response->status()) {
                401 => $this->handleUnauthorized($request),
                404 => throw new Exception('Not Found'),
                400 => throw new Exception('Bad Request'),
                default => throw new Exception('Unknown Error'),
            };
        }

        return $response->json();
    }

    private function handleUnauthorized(callable $request): array
    {
        $refresh = $this->strava->refreshToken();

        return $request()->json();
    }

    public function getActivity(int $id): array
    {
        $response = $this->strava->getActivity($id);

        if ($response->failed()) {
            $status = $response->status();
            throw new Exception('Failed to get activity');
        }

        return $response->json();
    }
}
