<?php

use JordanPartridge\StravaClient\Connector;
use Saloon\Http\Connector as BaseConnector;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('Connector Client extends a connector')
    ->expect(Connector::class)
    ->toExtend(BaseConnector::class);
