<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Http\Controllers\CallBackController;
use JordanPartridge\StravaClient\Models\StravaToken;
use JordanPartridge\StravaClient\StravaClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    Config::set('strava-client.redirect_after_connect', '/dashboard');

    $this->controller = new CallBackController;

    $this->connector = new Connector;
    $this->stravaClient = new StravaClient($this->connector);

    $this->user = Mockery::mock();
    $this->user->shouldReceive('getAuthIdentifier')->andReturn(123);

    $this->authProvider = Mockery::mock();
    Auth::shouldReceive('getProvider')->andReturn($this->authProvider);
});

afterEach(function () {
    Mockery::close();
});

it('handles valid callback and stores token', function () {
    $state = 'validState123';
    $code = 'authorizationCode';

    $request = Request::create('/callback', 'GET', [
        'state' => $state,
        'code' => $code,
    ]);

    Cache::shouldReceive('pull')
        ->with('strava_state:'.$state)
        ->once()
        ->andReturn(['user_id' => 123]);

    $this->authProvider->shouldReceive('retrieveById')
        ->with(123)
        ->once()
        ->andReturn($this->user);

    // Mock the HTTP response
    $mockClient = new MockClient([
        MockResponse::make([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
            'athlete' => ['id' => 456],
        ]),
    ]);

    $this->connector->withMockClient($mockClient);

    // We'll verify the token was created in the database after the request
    expect(StravaToken::count())->toBe(0);

    $response = $this->controller->__invoke($request, $this->stravaClient);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toContain(config('strava-client.redirect_after_connect'));

    // Verify token was created
    $token = StravaToken::where('user_id', 123)->first();
    expect($token)->not->toBeNull();
    expect($token->athlete_id)->toBe('456');
});

it('aborts with 400 for empty state', function () {
    $request = Request::create('/callback', 'GET', [
        'state' => '',
        'code' => 'authorizationCode',
    ]);

    expect(fn () => $this->controller->__invoke($request, $this->stravaClient))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('aborts with 400 for invalid state format', function () {
    $request = Request::create('/callback', 'GET', [
        'state' => 'invalid-state!@#',
        'code' => 'authorizationCode',
    ]);

    expect(fn () => $this->controller->__invoke($request, $this->stravaClient))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('aborts with 400 when state not found in cache', function () {
    $request = Request::create('/callback', 'GET', [
        'state' => 'nonExistentState',
        'code' => 'authorizationCode',
    ]);

    Cache::shouldReceive('pull')
        ->with('strava_state:nonExistentState')
        ->once()
        ->andReturn(null);

    expect(fn () => $this->controller->__invoke($request, $this->stravaClient))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('aborts with 400 when state data is invalid', function () {
    $request = Request::create('/callback', 'GET', [
        'state' => 'validState',
        'code' => 'authorizationCode',
    ]);

    Cache::shouldReceive('pull')
        ->once()
        ->andReturn(['invalid' => 'data']);

    expect(fn () => $this->controller->__invoke($request, $this->stravaClient))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('aborts with 404 when user not found', function () {
    $request = Request::create('/callback', 'GET', [
        'state' => 'validState',
        'code' => 'authorizationCode',
    ]);

    Cache::shouldReceive('pull')
        ->once()
        ->andReturn(['user_id' => 123]);

    $this->authProvider->shouldReceive('retrieveById')
        ->with(123)
        ->once()
        ->andReturn(null);

    expect(fn () => $this->controller->__invoke($request, $this->stravaClient))
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('correctly calculates token expiration time', function () {
    $state = 'validState';
    $request = Request::create('/callback', 'GET', [
        'state' => $state,
        'code' => 'authorizationCode',
    ]);

    Cache::shouldReceive('pull')->andReturn(['user_id' => 123]);
    $this->authProvider->shouldReceive('retrieveById')->andReturn($this->user);

    // Mock the HTTP response
    $mockClient = new MockClient([
        MockResponse::make([
            'access_token' => 'token',
            'refresh_token' => 'refresh',
            'expires_in' => 3600,
            'athlete' => ['id' => 456],
        ]),
    ]);

    $this->connector->withMockClient($mockClient);

    // We'll verify the expiration time after creation

    $this->controller->__invoke($request, $this->stravaClient);

    // Verify token expiration
    $token = StravaToken::where('user_id', 123)->first();
    expect($token)->not->toBeNull();

    $expectedExpiration = now()->addSeconds(3600);
    $actualExpiration = $token->expires_at;

    // Check that expiration is within 5 seconds of expected
    expect($actualExpiration->diffInSeconds($expectedExpiration))->toBeLessThan(5);
});
