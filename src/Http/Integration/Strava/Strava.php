<?php

namespace JordanPartridge\StravaClient\Http\Integration\Strava;

use Illuminate\Http\JsonResponse;
use JordanPartridge\StravaClient\Http\Integration\Strava\Requests\TokenExchange;
use Saloon\Http\Connector;

class Strava extends Connector
{
    private string $token;

    /**
     * Set the bearer token once we have it, not sure if this will
     * eventually be handled internally but as of right now I'm trying
     * to make sure all the functionality gets handled.
     * @param string $token
     * @return void
     */
    public function setToken(string $token)
    {
    }

    /**
     * @todo Error handling
     * @param string $code
     * @throws \JsonException
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     * @return JsonResponse
     */
    public function exchangeToken(string $code): array
    {
        $request     = new TokenExchange($code);
        $response    = $this->send($request);

        return $response->json();
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
}
