<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JordanPartridge\StravaClient\Events\ActivityCreated;
use JordanPartridge\StravaClient\Events\ActivityDeleted;
use JordanPartridge\StravaClient\Events\ActivityUpdated;
use JordanPartridge\StravaClient\Events\AthleteDeauthorized;
use JordanPartridge\StravaClient\Http\Controllers\WebhookController;
use JordanPartridge\StravaClient\Services\WebhookVerificationService;

beforeEach(function () {
    $this->verificationService = Mockery::mock(WebhookVerificationService::class);
    $this->controller = new WebhookController($this->verificationService);
    
    config(['strava-client.webhook.verify_token' => 'test-verify-token']);
});

it('handles subscription verification challenge', function () {
    $request = Request::create('/webhook', 'GET', [
        'hub_verify_token' => 'test-verify-token',
        'hub_challenge' => 'test-challenge-123',
    ]);

    $response = $this->controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
    expect($response->getData(true))->toBe(['hub.challenge' => 'test-challenge-123']);
});

it('aborts verification with invalid token', function () {
    $request = Request::create('/webhook', 'GET', [
        'hub_verify_token' => 'invalid-token',
        'hub_challenge' => 'test-challenge-123',
    ]);

    expect(fn () => $this->controller->__invoke($request))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('processes activity created webhook', function () {
    Event::fake();
    
    $this->verificationService->shouldReceive('verify')->once()->andReturn(true);

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode([
        'aspect_type' => 'create',
        'event_time' => 1234567890,
        'object_id' => 12345,
        'object_type' => 'activity',
        'owner_id' => 67890,
        'subscription_id' => 1,
    ]));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
    expect($response->getData(true))->toBe(['status' => 'success']);
    
    Event::assertDispatched(ActivityCreated::class);
});

it('processes activity updated webhook', function () {
    Event::fake();
    
    $this->verificationService->shouldReceive('verify')->once()->andReturn(true);

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode([
        'aspect_type' => 'update',
        'event_time' => 1234567890,
        'object_id' => 12345,
        'object_type' => 'activity',
        'owner_id' => 67890,
        'subscription_id' => 1,
        'updates' => ['title' => true],
    ]));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
    Event::assertDispatched(ActivityUpdated::class);
});

it('processes activity deleted webhook', function () {
    Event::fake();
    
    $this->verificationService->shouldReceive('verify')->once()->andReturn(true);

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode([
        'aspect_type' => 'delete',
        'event_time' => 1234567890,
        'object_id' => 12345,
        'object_type' => 'activity',
        'owner_id' => 67890,
        'subscription_id' => 1,
    ]));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
    Event::assertDispatched(ActivityDeleted::class);
});

it('processes athlete deauthorization webhook', function () {
    Event::fake();
    
    $this->verificationService->shouldReceive('verify')->once()->andReturn(true);

    $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode([
        'aspect_type' => 'update',
        'event_time' => 1234567890,
        'object_id' => 67890,
        'object_type' => 'athlete',
        'owner_id' => 67890,
        'subscription_id' => 1,
        'updates' => ['authorized' => 'false'],
    ]));
    $request->headers->set('Content-Type', 'application/json');

    $response = $this->controller->__invoke($request);

    expect($response->getStatusCode())->toBe(200);
    Event::assertDispatched(AthleteDeauthorized::class);
});