<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Facade;
use JordanPartridge\StravaClient\Connector;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector as BaseConnector;
use Saloon\Http\Request;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('Connector Client extends a connector')
    ->expect(Connector::class)
    ->toExtend(BaseConnector::class);

arch('Request execeptions to extend RequestException')
    ->expect('JordanPartridge\StravaClient\Exceptions\Request'
    )->toExtend(RequestException::class);

arch('Authorization exceptions to extend AuthorizationException')
    ->expect('JordanPartridge\StravaClient\Exceptions\Authentication')
    ->toExtend(AuthorizationException::class);

arch('Requests extend saloon Request')
    ->expect('JordanPartridge\StravaClient\Requests')
    ->toExtend(Request::class);

arch('Facades extend Facades')
    ->expect('JordanPartridge\StravaClient\Facades')
    ->toExtend(Facade::class);
