<?php

use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Exceptions\Authentication\MaxAttemptsException;
use JordanPartridge\StravaClient\Exceptions\Request\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\Request\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\Request\ResourceNotFoundException;
use JordanPartridge\StravaClient\Exceptions\Request\StravaServiceException;
use JordanPartridge\StravaClient\StravaClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->connector = new Connector;
    $this->client = new StravaClient($this->connector);
});

describe('constructor', function () {
    it('accepts custom max refresh attempts', function () {
        $client = new StravaClient($this->connector, 5);
        expect($client)->toBeInstanceOf(StravaClient::class);
    });

    it('throws exception for invalid max refresh attempts', function () {
        expect(fn () => new StravaClient($this->connector, 0))
            ->toThrow(InvalidArgumentException::class, 'Max refresh attempts must be greater than 0.');
    });
});

describe('setToken', function () {
    it('sets access and refresh tokens', function () {
        $this->client->setToken('access_token', 'refresh_token');
        expect(true)->toBeTrue(); // Just verify it doesn't throw
    });

    it('throws exception when access token is empty', function () {
        expect(fn () => $this->client->setToken('', 'refresh_token'))
            ->toThrow(InvalidArgumentException::class, 'Access and refresh tokens must be set');
    });

    it('throws exception when refresh token is empty', function () {
        expect(fn () => $this->client->setToken('access_token', ''))
            ->toThrow(InvalidArgumentException::class, 'Access and refresh tokens must be set');
    });
});

describe('exchangeToken', function () {
    it('exchanges authorization code for tokens', function () {
        $mockClient = new MockClient([
            MockResponse::make([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
                'athlete' => ['id' => 12345],
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $result = $this->client->exchangeToken('authorization_code');

        expect($result)
            ->toHaveKey('access_token', 'new_access_token')
            ->toHaveKey('refresh_token', 'new_refresh_token');
    });

    it('handles failed token exchange', function () {
        $mockClient = new MockClient([
            MockResponse::make([], 400),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $this->client->exchangeToken('invalid_code'))
            ->toThrow(BadRequestException::class);
    });
});

describe('activityForAthlete', function () {
    beforeEach(function () {
        $this->client->setToken('valid_token', 'refresh_token');
    });

    it('retrieves activities for authenticated athlete', function () {
        $mockClient = new MockClient([
            MockResponse::make([
                ['id' => 1, 'name' => 'Morning Run'],
                ['id' => 2, 'name' => 'Evening Ride'],
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $result = $this->client->activityForAthlete(1, 10);

        expect($result)->toHaveCount(2);
        expect($result[0])->toHaveKey('name', 'Morning Run');
    });

    it('throws exception for invalid page number', function () {
        expect(fn () => $this->client->activityForAthlete(0, 10))
            ->toThrow(InvalidArgumentException::class, 'Page and per_page must be positive integers');
    });

    it('throws exception for invalid per_page number', function () {
        expect(fn () => $this->client->activityForAthlete(1, 0))
            ->toThrow(InvalidArgumentException::class, 'Page and per_page must be positive integers');
    });

    it('handles rate limit exceeded', function () {
        $mockClient = new MockClient([
            MockResponse::make([], 429),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $this->client->activityForAthlete(1, 10))
            ->toThrow(RateLimitExceededException::class);
    });
});

describe('getActivity', function () {
    beforeEach(function () {
        $this->client->setToken('valid_token', 'refresh_token');
    });

    it('retrieves a specific activity by ID', function () {
        $mockClient = new MockClient([
            MockResponse::make([
                'id' => 12345,
                'name' => 'Morning Run',
                'distance' => 5000,
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $result = $this->client->getActivity(12345);

        expect($result)
            ->toHaveKey('id', 12345)
            ->toHaveKey('name', 'Morning Run');
    });

    it('throws exception for invalid activity ID', function () {
        expect(fn () => $this->client->getActivity(0))
            ->toThrow(InvalidArgumentException::class, 'Activity ID must be positive');
    });

    it('handles not found response', function () {
        $mockClient = new MockClient([
            MockResponse::make([], 404),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $this->client->getActivity(99999))
            ->toThrow(ResourceNotFoundException::class);
    });

    it('handles server errors', function () {
        $mockClient = new MockClient([
            MockResponse::make([], 500),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $this->client->getActivity(12345))
            ->toThrow(StravaServiceException::class);
    });
});

describe('token refresh', function () {
    beforeEach(function () {
        $this->client->setToken('expired_token', 'valid_refresh_token');
    });

    it('automatically refreshes token on 401 response', function () {
        $mockClient = new MockClient([
            // First request returns 401
            MockResponse::make([], 401),
            // Token refresh request
            MockResponse::make([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
            ]),
            // Retry original request with new token
            MockResponse::make([
                'id' => 12345,
                'name' => 'Morning Run',
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $result = $this->client->getActivity(12345);

        expect($result)->toHaveKey('name', 'Morning Run');
    });

    it('throws MaxAttemptsException after exceeding retry limit', function () {
        $client = new StravaClient($this->connector, 2);
        $client->setToken('expired_token', 'invalid_refresh_token');

        $mockClient = new MockClient([
            // First request returns 401
            MockResponse::make([], 401),
            // Token refresh also returns 401
            MockResponse::make([], 401),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $client->getActivity(12345))
            ->toThrow(MaxAttemptsException::class, 'Maximum token refresh attempts exceeded');
    });
});

describe('service unavailable handling', function () {
    beforeEach(function () {
        $this->client->setToken('valid_token', 'refresh_token');
    });

    it('retries on 503 with exponential backoff', function () {
        $startTime = microtime(true);

        $mockClient = new MockClient([
            // First request returns 503
            MockResponse::make([], 503),
            // Second request returns 503
            MockResponse::make([], 503),
            // Third request succeeds
            MockResponse::make([
                'id' => 12345,
                'name' => 'Morning Run',
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $result = $this->client->getActivity(12345);

        $elapsedTime = microtime(true) - $startTime;

        expect($result)->toHaveKey('name', 'Morning Run');
        // Should have waited at least 3 seconds (1s + 2s delays)
        expect($elapsedTime)->toBeGreaterThan(3.0);
    });

    it('throws exception after max retry attempts on 503', function () {
        $mockClient = new MockClient([
            // All requests return 503
            MockResponse::make([], 503),
            MockResponse::make([], 503),
            MockResponse::make([], 503),
            MockResponse::make([], 503),
        ]);

        $this->connector->withMockClient($mockClient);

        expect(fn () => $this->client->getActivity(12345))
            ->toThrow(\RuntimeException::class, 'Strava service unavailable after 4 attempts');
    });
});
