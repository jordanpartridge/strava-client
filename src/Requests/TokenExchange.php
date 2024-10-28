<?php

namespace JordanPartridge\StravaClient\Requests;

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
     *  Either an authorization code or a refresh token.
     */
    private string $token;

    /**
     * The type of grant being exchanged
     */
    private string $grant_type;

    public function __construct(string $code, string $grant_type = 'authorization_code')
    {
        $this->token = $code;
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
     * The body slightly changes depending on the grant type
     * so essentially returns a code or refresh token.
     *
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'grant_type' => $this->grant_type,
            $this->getTokenLabel() => $this->token,
        ];
    }

    private function getTokenLabel(): string
    {
        return $this->grant_type === 'authorization_code' ? 'code' : 'refresh_token';
    }
}
