<?php

use JordanPartridge\StravaClient\Connector;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector as BaseConnector;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('Connector Client extends a connector')
    ->expect(Connector::class)
    ->toExtend(BaseConnector::class);

arch('Exceptions to extend RequestException')
    ->expect('JordanPartridge\StravaClient\Exceptions'
    )->toExtend(RequestException::class);
