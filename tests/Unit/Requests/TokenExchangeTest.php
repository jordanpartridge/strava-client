<?php

use JordanPartridge\StravaClient\Requests\TokenExchange;
use Saloon\Enums\Method;

it('creates token exchange request with correct configuration', function () {
    $request = new TokenExchange('auth_code_123', 'authorization_code');

    expect($request->getMethod())->toBe(Method::POST);
    expect($request->resolveEndpoint())->toBe('/oauth/token');
});

it('includes correct body for authorization code exchange', function () {
    $request = new TokenExchange('auth_code_123', 'authorization_code');
    $body = $request->defaultBody();

    expect($body)->toHaveKey('code', 'auth_code_123');
    expect($body)->toHaveKey('grant_type', 'authorization_code');
    expect($body)->toHaveKey('client_id');
    expect($body)->toHaveKey('client_secret');
});

it('includes correct body for refresh token exchange', function () {
    $request = new TokenExchange('refresh_token_123', 'refresh_token');
    $body = $request->defaultBody();

    expect($body)->toHaveKey('refresh_token', 'refresh_token_123');
    expect($body)->toHaveKey('grant_type', 'refresh_token');
    expect($body)->toHaveKey('client_id');
    expect($body)->toHaveKey('client_secret');
});

it('uses client credentials from config', function () {
    config(['strava-client.client_id' => 'test_client_id']);
    config(['strava-client.client_secret' => 'test_client_secret']);

    $request = new TokenExchange('code', 'authorization_code');
    $body = $request->defaultBody();

    expect($body['client_id'])->toBe('test_client_id');
    expect($body['client_secret'])->toBe('test_client_secret');
});
