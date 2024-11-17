<?php

namespace JordanPartridge\StravaClient\Facades;

use Illuminate\Support\Facades\Facade;
use JordanPartridge\StravaClient\StravaClient as StravaClientService;

/**
 * @see \JordanPartridge\StravaClient\StravaClient
 */
class StravaClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'strava-client';
    }
