<?php

use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Data\Webhooks\SubscriptionData;
use JordanPartridge\StravaClient\Resources\WebhookResource;
use Saloon\Http\Response;

beforeEach(function () {
    $this->connector = Mockery::mock(Connector::class);
    $this->resource = new WebhookResource($this->connector);
    
    config([
        'strava-client.webhook.callback_url' => 'https://example.com/webhook',
        'strava-client.webhook.verify_token' => 'test-token',
    ]);
});

it('creates a webhook subscription', function () {
    $responseData = [
        'id' => 123,
        'callback_url' => 'https://example.com/webhook',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-01T00:00:00Z',
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('createWebhookSubscription')
        ->with('https://example.com/webhook', 'test-token')
        ->andReturn($response);

    $subscription = $this->resource->create();

    expect($subscription)->toBeInstanceOf(SubscriptionData::class);
    expect($subscription->id)->toBe(123);
    expect($subscription->callback_url)->toBe('https://example.com/webhook');
});

it('creates a webhook subscription with custom parameters', function () {
    $responseData = [
        'id' => 456,
        'callback_url' => 'https://custom.com/webhook',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-01T00:00:00Z',
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('createWebhookSubscription')
        ->with('https://custom.com/webhook', 'custom-token')
        ->andReturn($response);

    $subscription = $this->resource->create('https://custom.com/webhook', 'custom-token');

    expect($subscription->id)->toBe(456);
    expect($subscription->callback_url)->toBe('https://custom.com/webhook');
});

it('throws exception when creating webhook without required parameters', function () {
    config(['strava-client.webhook.callback_url' => '']);

    expect(fn () => $this->resource->create())
        ->toThrow(InvalidArgumentException::class, 'Callback URL and verify token are required');
});

it('deletes a webhook subscription', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('deleteWebhookSubscription')
        ->with(123)
        ->andReturn($response);

    $result = $this->resource->delete(123);

    expect($result)->toBeTrue();
});

it('lists webhook subscriptions', function () {
    $responseData = [
        [
            'id' => 123,
            'callback_url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-01T00:00:00Z',
        ],
        [
            'id' => 456,
            'callback_url' => 'https://another.com/webhook',
            'created_at' => '2023-01-02T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    $subscriptions = $this->resource->list();

    expect($subscriptions)->toHaveCount(2);
    expect($subscriptions[0])->toBeInstanceOf(SubscriptionData::class);
    expect($subscriptions[0]->id)->toBe(123);
    expect($subscriptions[1]->id)->toBe(456);
});

it('finds a specific webhook subscription', function () {
    $responseData = [
        [
            'id' => 123,
            'callback_url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-01T00:00:00Z',
        ],
        [
            'id' => 456,
            'callback_url' => 'https://another.com/webhook',
            'created_at' => '2023-01-02T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    $subscription = $this->resource->find(456);

    expect($subscription)->toBeInstanceOf(SubscriptionData::class);
    expect($subscription->id)->toBe(456);
});

it('returns null when subscription not found', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    $subscription = $this->resource->find(999);

    expect($subscription)->toBeNull();
});

it('checks if webhooks exist', function () {
    $responseData = [
        [
            'id' => 123,
            'callback_url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-01T00:00:00Z',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    expect($this->resource->exists())->toBeTrue();
});

it('returns false when no webhooks exist', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    expect($this->resource->exists())->toBeFalse();
});

it('gets the first webhook subscription', function () {
    $responseData = [
        [
            'id' => 123,
            'callback_url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-01T00:00:00Z',
        ],
        [
            'id' => 456,
            'callback_url' => 'https://another.com/webhook',
            'created_at' => '2023-01-02T00:00:00Z',
            'updated_at' => '2023-01-02T00:00:00Z',
        ],
    ];

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn($responseData);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    $subscription = $this->resource->first();

    expect($subscription)->toBeInstanceOf(SubscriptionData::class);
    expect($subscription->id)->toBe(123);
});

it('returns null when no first subscription exists', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->andReturn([]);

    $this->connector
        ->shouldReceive('viewWebhookSubscriptions')
        ->andReturn($response);

    $subscription = $this->resource->first();

    expect($subscription)->toBeNull();
});