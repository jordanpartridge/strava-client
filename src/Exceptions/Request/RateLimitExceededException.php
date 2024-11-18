<?php

namespace JordanPartridge\StravaClient\Exceptions\Request;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class RateLimitExceededException extends RequestException
{
    public function __construct(Response $response, string $message = 'Rate limit exceeded')
    {
        parent::__construct(response: $response, message: $message, code: 429);
    }
}
