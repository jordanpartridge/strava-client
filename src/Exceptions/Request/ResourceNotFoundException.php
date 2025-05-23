<?php

namespace JordanPartridge\StravaClient\Exceptions\Request;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class ResourceNotFoundException extends RequestException
{
    public function __construct(Response $response, string $message = 'Resource not found')
    {
        parent::__construct(response: $response, message: $message, code: 404);
    }
}
