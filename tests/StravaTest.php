<?php

use JordanPartridge\StravaClient\Connector;

it('has proper base url', function () {
    $connector = new Connector;
    expect($connector->resolveBaseUrl())->toBe('https://www.strava.com/api/v3');
});
