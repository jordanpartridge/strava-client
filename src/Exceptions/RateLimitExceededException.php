<?php

namespace JordanPartridge\StravaClient\Exceptions;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class RateLimitExceededException extends RequestException
{
    public function __construct(Response $response, string $message = 'Rate limit exceeded')
    {
        parent::__construct($response, $message);
    }
}
