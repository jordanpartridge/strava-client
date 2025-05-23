<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use JordanPartridge\StravaClient\Http\Controllers\RedirectController;

beforeEach(function () {
    Config::set('strava-client.client_id', 'test_client_id');
    Config::set('strava-client.authorize_url', 'https://strava.com/oauth/authorize');
    Config::set('strava-client.scope', 'read,activity:read_all');

    // Register the route for testing
    Route::get('/strava/callback', fn () => null)->name('strava:callback');

    $this->controller = new RedirectController;
    $this->user = Mockery::mock();
    $this->user->shouldReceive('getAuthIdentifier')->andReturn(123);

    $this->request = Mockery::mock(Request::class);
    $this->request->shouldReceive('user')->andReturn($this->user);
});

afterEach(function () {
    Mockery::close();
});

it('redirects to Strava authorization URL with correct parameters', function () {
    Cache::shouldReceive('put')
        ->once()
        ->withArgs(function ($key, $data) {
            expect($key)->toStartWith('strava_state:');
            expect($data)->toHaveKeys(['user_id', 'timestamp']);
            expect($data['user_id'])->toBe(123);

            return true;
        });

    $response = $this->controller->__invoke($this->request);

    expect($response->getStatusCode())->toBe(302);

    $location = $response->headers->get('Location');
    expect($location)->toContain('https://strava.com/oauth/authorize');
    expect($location)->toContain('client_id=test_client_id');
    expect($location)->toContain('response_type=code');
    expect($location)->toContain('scope=read%2Cactivity%3Aread_all');
    expect($location)->toContain('state=');
});

it('generates unique state parameter', function () {
    $states = [];

    Cache::shouldReceive('put')
        ->times(3)
        ->withArgs(function ($key) use (&$states) {
            $state = str_replace('strava_state:', '', $key);
            $states[] = $state;

            return true;
        });

    for ($i = 0; $i < 3; $i++) {
        $this->controller->__invoke($this->request);
    }

    expect($states)->toHaveCount(3);
    expect(array_unique($states))->toHaveCount(3);
});

it('throws exception when client ID is not configured', function () {
    Config::set('strava-client.client_id', '');

    expect(fn () => $this->controller->__invoke($this->request))
        ->toThrow(RuntimeException::class, 'Strava client ID is not configured');
});

it('throws exception when cache operation fails', function () {
    Cache::shouldReceive('put')
        ->once()
        ->andThrow(new RuntimeException('Cache write failed'));

    expect(fn () => $this->controller->__invoke($this->request))
        ->toThrow(RuntimeException::class, 'Authentication flow initialization failed');
});

it('stores state with 10 minute expiration', function () {
    $expirationTime = null;

    Cache::shouldReceive('put')
        ->once()
        ->withArgs(function ($key, $data, $expiry) use (&$expirationTime) {
            $expirationTime = $expiry;

            return true;
        });

    $this->controller->__invoke($this->request);

    // Check that expiration is approximately 10 minutes from now
    $diffInMinutes = now()->diffInMinutes($expirationTime);
    expect($diffInMinutes)->toBeGreaterThanOrEqual(9);
    expect($diffInMinutes)->toBeLessThanOrEqual(11);
});

it('validates state parameter format', function () {
    Cache::shouldReceive('put')
        ->once()
        ->withArgs(function ($key) {
            $state = str_replace('strava_state:', '', $key);
            // State should be 32 characters of alphanumeric
            expect($state)->toMatch('/^[a-zA-Z0-9]{32}$/');

            return true;
        });

    $this->controller->__invoke($this->request);
});
