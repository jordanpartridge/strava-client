<?php

use JordanPartridge\StravaClient\Http\Integration\Strava\Strava;
use Saloon\Http\Connector;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('Strava Client extends a connector')
    ->expect(Strava::class)
    ->toExtend(Connector::class);
