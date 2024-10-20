<?php

namespace JordanPartridge\StravaClient\Http\Integration\Strava\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TokenExchange extends Request implements HasBody
{
    use HasJsonBody;


    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The code from the Strava OAuth flow, either an authorization code or a refresh token
     */
    private string $code;

    /**
     * The type of grant being exchanged
     */
    private string $grant_type;

    public function __construct(string $code, string $grant_type = 'authorization_code')
    {
        $this->code = $code;
        $this->grant_type = $grant_type;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/oauth/token';
    }

    /**
     * @todo: this is a little confusing, let's extract functionality
     *       for the request body maybe traits of HandlesGrant or maybe an adapter
     *       of sort? Also might be worth looking to see if Saloon 3 has some new
     *       sorts of things that might help with this.
     * @return array<string, mixed>
     *
     *
     */
    public function defaultBody(): array
    {
        return $this->grant_type === 'authorization_code' ? [
            'client_id'     => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'code'          => $this->code,
            'grant_type'    => $this->grant_type,
        ] : [
            'client_id'     => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'refresh_token' => $this->code,
            'grant_type'    => $this->grant_type,
        ];
    }
}
