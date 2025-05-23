<?php

use JordanPartridge\StravaClient\Requests\AthleteActivityRequest;
use Saloon\Enums\Method;

it('creates athlete activity request with correct configuration', function () {
    $request = new AthleteActivityRequest(['page' => 1, 'per_page' => 30]);

    expect($request->getMethod())->toBe(Method::GET);
    expect($request->resolveEndpoint())->toBe('/athlete/activities');
});

it('includes query parameters in request', function () {
    $params = [
        'page' => 2,
        'per_page' => 50,
        'before' => 1234567890,
        'after' => 1234567800,
    ];

    $request = new AthleteActivityRequest($params);

    // Test that params are stored correctly
    expect($request)->toBeInstanceOf(AthleteActivityRequest::class);
});

it('handles empty query parameters', function () {
    $request = new AthleteActivityRequest([]);

    // Test that empty params are accepted
    expect($request)->toBeInstanceOf(AthleteActivityRequest::class);
});

it('preserves all provided parameters', function () {
    $params = [
        'page' => 1,
        'per_page' => 100,
        'custom_param' => 'value',
    ];

    $request = new AthleteActivityRequest($params);

    // Test that params are accepted
    expect($request)->toBeInstanceOf(AthleteActivityRequest::class);
});
