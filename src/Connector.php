<?php

namespace JordanPartridge\StravaClient;

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
     * I think I do not need error handling here, I'm going to leave it to
     * the service to handle that I wonder if I can make this file more internal to
     * the package.
     *
     * @param string $code
     * @param string $grant_type
     * @throws FatalRequestException
     * @throws RequestException
     * @return Response
     */
    public function exchangeToken(string $code, string $grant_type): Response
    {
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
        return  $this->token ? new TokenAuthenticator($this->token) : null;
    }
}
