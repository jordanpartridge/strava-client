<?php

namespace JordanPartridge\StravaClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JordanPartridge\StravaClient\StravaClient
 */
class StravaClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'strava-client';
    }
}
