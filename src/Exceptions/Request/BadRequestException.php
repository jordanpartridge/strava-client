<?php

namespace JordanPartridge\StravaClient\Exceptions\Request;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class BadRequestException extends RequestException
{
    public function __construct(Response $response, $message = 'Bad request')
    {
        parent::__construct(response: $response, message: $message, code: 400);
    }
}
