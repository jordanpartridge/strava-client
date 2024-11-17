<?php

namespace JordanPartridge\StravaClient\Exceptions;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class BadRequestException extends RequestException
{
    public function __construct(Response $response, $message = 'Bad request')
    {
        parent::__construct($response, $message);
    }
}
