<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use JordanPartridge\StravaClient\Models\StravaToken;

uses(RefreshDatabase::class);

it('has correct fillable attributes', function () {
    $token = new StravaToken;

    expect($token->getFillable())->toBe([
        'access_token',
        'athlete_id',
        'refresh_token',
        'user_id',
        'expires_at',
    ]);
});

it('casts attributes correctly', function () {
    $token = new StravaToken;
    $casts = $token->getCasts();

    expect($casts['access_token'])->toBe('encrypted');
    expect($casts['refresh_token'])->toBe('encrypted');
    expect($casts['expires_at'])->toBe('datetime');
});

it('can create token with all attributes', function () {
    $token = StravaToken::create([
        'user_id' => 1,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'athlete_id' => '12345',
        'expires_at' => now()->addHour(),
    ]);

    expect($token)->toBeInstanceOf(StravaToken::class);
    expect($token->user_id)->toBe(1);
    expect($token->athlete_id)->toBe('12345');
    expect($token->expires_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

it('encrypts token fields when stored', function () {
    $token = StravaToken::create([
        'user_id' => 1,
        'access_token' => 'plain_access_token',
        'refresh_token' => 'plain_refresh_token',
        'athlete_id' => '12345',
        'expires_at' => now()->addHour(),
    ]);

    // Get raw values from database
    $rawToken = \DB::table('strava_tokens')->where('id', $token->id)->first();

    // Raw values should be encrypted (not equal to plain text)
    expect($rawToken->access_token)->not->toBe('plain_access_token');
    expect($rawToken->refresh_token)->not->toBe('plain_refresh_token');

    // But when accessed through model, they should be decrypted
    expect($token->access_token)->toBe('plain_access_token');
    expect($token->refresh_token)->toBe('plain_refresh_token');
});

it('handles null athlete_id', function () {
    $token = StravaToken::create([
        'user_id' => 1,
        'access_token' => 'test_token',
        'refresh_token' => 'test_refresh',
        'expires_at' => now()->addHour(),
    ]);

    expect($token->athlete_id)->toBeNull();
});

it('can check if token is expired', function () {
    $expiredToken = new StravaToken([
        'expires_at' => now()->subHour(),
    ]);

    $validToken = new StravaToken([
        'expires_at' => now()->addHour(),
    ]);

    // Note: This test assumes you'll add an isExpired() method
    // For now, we'll just test the expires_at attribute
    expect($expiredToken->expires_at->isPast())->toBeTrue();
    expect($validToken->expires_at->isFuture())->toBeTrue();
});
