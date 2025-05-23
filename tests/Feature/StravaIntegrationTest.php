<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Models\StravaToken;
use JordanPartridge\StravaClient\StravaClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'strava-client.client_id' => 'test_client_id',
        'strava-client.client_secret' => 'test_client_secret',
        'strava-client.redirect_after_connect' => '/dashboard',
        'strava-client.scope' => 'read,activity:read_all',
    ]);

    $this->user = createTestUser();
});

test('complete oauth flow from redirect to callback', function () {
    // Step 1: Test redirect to Strava
    $this->actingAs($this->user)
        ->get(route('strava:redirect'))
        ->assertRedirect()
        ->assertRedirectContains('client_id=test_client_id')
        ->assertRedirectContains('scope=read%2Cactivity%3Aread_all');

    // Extract state from redirect URL
    $response = $this->actingAs($this->user)->get(route('strava:redirect'));
    $location = $response->headers->get('Location');
    parse_str(parse_url($location, PHP_URL_QUERY), $query);
    $state = $query['state'] ?? null;

    expect($state)->not->toBeNull();

    // Step 2: Mock Strava's token exchange response
    $mockClient = new MockClient([
        MockResponse::make([
            'token_type' => 'Bearer',
            'access_token' => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token',
            'expires_in' => 3600,
            'athlete' => [
                'id' => 123456,
                'username' => 'testuser',
            ],
        ]),
    ]);

    $connector = new Connector;
    $connector->withMockClient($mockClient);

    $this->app->instance(Connector::class, $connector);

    // Step 3: Test callback handling
    $this->get(route('strava:callback', [
        'state' => $state,
        'code' => 'mock_auth_code',
    ]))
        ->assertRedirect('/dashboard')
        ->assertSessionHas('success', 'Strava connected successfully');

    // Step 4: Verify token was stored
    $token = StravaToken::where('user_id', $this->user->id)->first();

    expect($token)->not->toBeNull();
    expect($token->athlete_id)->toBe('123456');
    expect($token->access_token)->toBe('mock_access_token');
    expect($token->refresh_token)->toBe('mock_refresh_token');
});

test('fetching activities with automatic token refresh', function () {
    // Create an expired token
    $token = StravaToken::create([
        'user_id' => $this->user->id,
        'access_token' => 'expired_token',
        'refresh_token' => 'valid_refresh_token',
        'athlete_id' => '123456',
        'expires_at' => now()->subHour(),
    ]);

    // Mock responses: first 401, then token refresh, then successful activity fetch
    $mockClient = new MockClient([
        // First request returns 401 (unauthorized)
        MockResponse::make([], 401),

        // Token refresh response
        MockResponse::make([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ]),

        // Successful activity fetch
        MockResponse::make([
            [
                'id' => 1,
                'name' => 'Morning Run',
                'distance' => 5000,
                'moving_time' => 1800,
            ],
            [
                'id' => 2,
                'name' => 'Evening Ride',
                'distance' => 15000,
                'moving_time' => 3600,
            ],
        ]),
    ]);

    $connector = new Connector;
    $connector->withMockClient($mockClient);
    $connector->setToken($token->access_token, $token->refresh_token);

    $client = new StravaClient($connector);

    // Fetch activities
    $activities = $client->activityForAthlete(1, 10);

    expect($activities)->toHaveCount(2);
    expect($activities[0]['name'])->toBe('Morning Run');
});

test('handling rate limit with proper exception', function () {
    $token = createValidToken($this->user);

    $mockClient = new MockClient([
        MockResponse::make([
            'message' => 'Rate Limit Exceeded',
            'errors' => [
                [
                    'resource' => 'Application',
                    'field' => 'rate limit',
                    'code' => 'exceeded',
                ],
            ],
        ], 429),
    ]);

    $connector = new Connector;
    $connector->withMockClient($mockClient);
    $connector->setToken($token->access_token, $token->refresh_token);

    $client = new StravaClient($connector);

    expect(fn () => $client->activityForAthlete(1, 10))
        ->toThrow(\JordanPartridge\StravaClient\Exceptions\Request\RateLimitExceededException::class);
});

test('handling invalid oauth state', function () {
    $this->get(route('strava:callback', [
        'state' => 'invalid_state',
        'code' => 'some_code',
    ]))
        ->assertStatus(400);
});

test('concurrent token refresh attempts', function () {
    $token = createExpiredToken($this->user);

    $mockClient = new MockClient([
        // First request - 401
        MockResponse::make([], 401),
        // Token refresh
        MockResponse::make([
            'access_token' => 'refreshed_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 3600,
        ]),
        // Retry original request
        MockResponse::make(['id' => 123, 'name' => 'Test Activity']),
    ]);

    $connector = new Connector;
    $connector->withMockClient($mockClient);
    $connector->setToken($token->access_token, $token->refresh_token);

    $client = new StravaClient($connector, 3);

    $result = $client->getActivity(123);

    expect($result)->toHaveKey('name', 'Test Activity');
});

// Helper functions
function createTestUser()
{
    // Create a basic User model for testing
    $user = new class extends \Illuminate\Database\Eloquent\Model implements \Illuminate\Contracts\Auth\Authenticatable
    {
        use \Illuminate\Auth\Authenticatable;
        use \JordanPartridge\StravaClient\Concerns\HasStravaTokens;

        protected $table = 'users';

        protected $fillable = ['name', 'email', 'password'];
    };

    return $user::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
}

function createValidToken($user)
{
    return StravaToken::create([
        'user_id' => $user->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'valid_refresh',
        'athlete_id' => '123456',
        'expires_at' => now()->addHour(),
    ]);
}

function createExpiredToken($user)
{
    return StravaToken::create([
        'user_id' => $user->id,
        'access_token' => 'expired_token',
        'refresh_token' => 'valid_refresh',
        'athlete_id' => '123456',
        'expires_at' => now()->subHour(),
    ]);
}
