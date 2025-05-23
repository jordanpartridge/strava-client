<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JordanPartridge\StravaClient\Concerns\HasStravaTokens;
use JordanPartridge\StravaClient\Models\StravaToken;

uses(RefreshDatabase::class);

// Create a test user model that uses the trait
class TestUser extends Model
{
    use HasStravaTokens;

    protected $table = 'users';

    protected $fillable = ['name', 'email'];
}

beforeEach(function () {
    // Users table is created via test migrations
});

it('establishes hasOne relationship with StravaToken', function () {
    $user = new TestUser;
    $relation = $user->stravaToken();

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
    expect($relation->getRelated())->toBeInstanceOf(StravaToken::class);
});

it('can access strava token through relationship', function () {
    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $token = StravaToken::create([
        'user_id' => $user->id,
        'access_token' => 'test_access',
        'refresh_token' => 'test_refresh',
        'athlete_id' => '12345',
        'expires_at' => now()->addHour(),
    ]);

    $userToken = $user->stravaToken;

    expect($userToken)->toBeInstanceOf(StravaToken::class);
    expect($userToken->id)->toBe($token->id);
    expect($userToken->athlete_id)->toBe('12345');
});

it('returns null when user has no strava token', function () {
    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    expect($user->stravaToken)->toBeNull();
});

it('can create strava token through relationship', function () {
    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $token = $user->stravaToken()->create([
        'access_token' => 'new_access',
        'refresh_token' => 'new_refresh',
        'athlete_id' => '67890',
        'expires_at' => now()->addHours(2),
    ]);

    expect($token)->toBeInstanceOf(StravaToken::class);
    expect($token->user_id)->toBe($user->id);
    expect($token->athlete_id)->toBe('67890');
});

it('maintains one-to-one relationship', function () {
    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    // Create first token
    $firstToken = $user->stravaToken()->create([
        'access_token' => 'first_access',
        'refresh_token' => 'first_refresh',
        'athlete_id' => '11111',
        'expires_at' => now()->addHour(),
    ]);

    // Create second token (should be separate, not replace)
    StravaToken::create([
        'user_id' => $user->id,
        'access_token' => 'second_access',
        'refresh_token' => 'second_refresh',
        'athlete_id' => '22222',
        'expires_at' => now()->addHour(),
    ]);

    // User should only have access to the first token through the relationship
    // (This demonstrates that hasOne returns the first matching record)
    expect($user->fresh()->stravaToken->id)->toBe($firstToken->id);
});
