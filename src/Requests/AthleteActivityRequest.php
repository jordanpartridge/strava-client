<?php

namespace JordanPartridge\StravaClient\Requests;

use InvalidArgumentException;
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
    ) {
        if (! isset($payload['page']) || ! isset($payload['per_page'])) {
            throw new InvalidArgumentException('Page and per_page are required');
        }
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/athlete/activities';
    }

    public function resolveQuery(): array
    {
        return [
            'page' => $this->payload['page'],
        ];
    }
}
