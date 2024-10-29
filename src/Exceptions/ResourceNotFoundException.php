<?php

namespace JordanPartridge\StravaClient\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    public function __construct(callable $request)
    {
        $this->message = $request()->json()['message'];
        parent::__construct($this->message);
    }
}
