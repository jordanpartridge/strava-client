<?php

namespace JordanPartridge\StravaClient\Exceptions;

use Exception;
use Saloon\Http\Response;

class ResourceNotFoundException extends Exception
{
    private Response $response;

    public function __construct(Response $response, ?string $message = null)
    {
        $this->response = $response;
        $responseMessage = $response->json('message');

        parent::__construct(
            $message ?? $responseMessage ?? 'Resource not found',
            $response->status()
        );
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
