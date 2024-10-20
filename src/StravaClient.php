<?php

namespace JordanPartridge\StravaClient;

use JordanPartridge\StravaClient\Http\Integration\Strava\Strava;

class StravaClient {

    public function __construct(private Strava $strava)
    {
        //
    }
}
