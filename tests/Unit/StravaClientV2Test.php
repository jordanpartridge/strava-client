<?php

use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Resources\ActivityResource;
use JordanPartridge\StravaClient\Resources\AthleteResource;
use JordanPartridge\StravaClient\Resources\WebhookResource;
use JordanPartridge\StravaClient\StravaClientV2;
use Saloon\Http\Response;

beforeEach(function () {
    $this->connector = Mockery::mock(Connector::class);
    $this->client = new StravaClientV2($this->connector);
});

describe('Resource Access', function () {
    it('provides activity resource', function () {
        $resource = $this->client->activities();
        
        expect($resource)->toBeInstanceOf(ActivityResource::class);
        
        // Ensure same instance is returned (cached)
        expect($this->client->activities())->toBe($resource);
    });

    it('provides athlete resource', function () {
        $resource = $this->client->athlete();
        
        expect($resource)->toBeInstanceOf(AthleteResource::class);
        expect($this->client->athlete())->toBe($resource);
    });

    it('provides webhook resource', function () {
        $resource = $this->client->webhooks();
        
        expect($resource)->toBeInstanceOf(WebhookResource::class);
        expect($this->client->webhooks())->toBe($resource);
    });
});

describe('Token Management', function () {
    it('sets tokens via legacy interface', function () {
        $this->connector
            ->shouldReceive('setToken')
            ->with('access-token', 'refresh-token')
            ->once();

        $result = $this->client->setToken('access-token', 'refresh-token');

        expect($result)->toBe($this->client);
    });

    it('sets tokens via modern interface', function () {
        $this->connector
            ->shouldReceive('setToken')
            ->with('access-token', 'refresh-token')
            ->once();

        $result = $this->client->withTokens('access-token', 'refresh-token');

        expect($result)->toBe($this->client);
    });

    it('throws exception for empty access token', function () {
        expect(fn () => $this->client->setToken('', 'refresh-token'))
            ->toThrow(InvalidArgumentException::class, 'Access token cannot be empty');
    });

    it('throws exception for empty refresh token', function () {
        expect(fn () => $this->client->setToken('access-token', ''))
            ->toThrow(InvalidArgumentException::class, 'Refresh token cannot be empty');
    });
});

describe('Legacy API Compatibility', function () {
    it('has legacy methods available', function () {
        // Just verify the methods exist and are callable
        expect(method_exists($this->client, 'activityForAthlete'))->toBeTrue();
        expect(method_exists($this->client, 'getActivity'))->toBeTrue();
        expect(method_exists($this->client, 'createWebhookSubscription'))->toBeTrue();
        expect(method_exists($this->client, 'deleteWebhookSubscription'))->toBeTrue();
        expect(method_exists($this->client, 'viewWebhookSubscriptions'))->toBeTrue();
    });
});

describe('Token Exchange', function () {
    it('exchanges authorization code for tokens', function () {
        $responseData = [
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_at' => 1234567890,
        ];

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn($responseData);

        $this->connector
            ->shouldReceive('exchangeToken')
            ->with('auth-code', 'authorization_code')
            ->andReturn($response);

        $result = $this->client->exchangeToken('auth-code');

        expect($result)->toBe($responseData);
    });

    it('throws exception on failed token exchange', function () {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(false);
        $response->shouldReceive('status')->andReturn(400);

        $this->connector
            ->shouldReceive('exchangeToken')
            ->with('invalid-code', 'authorization_code')
            ->andReturn($response);

        expect(fn () => $this->client->exchangeToken('invalid-code'))
            ->toThrow(\RuntimeException::class);
    });
});

describe('Constructor Validation', function () {
    it('throws exception for invalid max refresh attempts', function () {
        expect(fn () => new StravaClientV2($this->connector, 0))
            ->toThrow(InvalidArgumentException::class, 'Max refresh attempts must be greater than 0');
    });

    it('accepts valid max refresh attempts', function () {
        $client = new StravaClientV2($this->connector, 5);

        expect($client)->toBeInstanceOf(StravaClientV2::class);
    });
});