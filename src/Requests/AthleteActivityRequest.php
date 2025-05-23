<?php

namespace JordanPartridge\StravaClient\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class AthleteActivityRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        private readonly array $payload,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/athlete/activities';
    }

    protected function defaultQuery(): array
    {
        return $this->payload;
    }
}
