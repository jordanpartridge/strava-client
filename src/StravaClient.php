<?php

namespace JordanPartridge\StravaClient;

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

    public function setToken(string $token): void
    {
        $this->strava->setToken($token);
    }
    public function activityForAthlete($page, $per_page): array
    {
        return $this->strava->activityForAthlete(['page' => $page, 'per_page' => $per_page])->json();
    }
}
