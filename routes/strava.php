<?php

use Illuminate\Support\Facades\Route;
use JordanPartridge\StravaClient\Http\Controllers\CallBackController;
use JordanPartridge\StravaClient\Http\Controllers\RedirectController;
use JordanPartridge\StravaClient\Http\Controllers\WebhookController;

Route::prefix('strava')
    ->as('strava:')
    ->middleware(['web', config('auth.defaults.guard')])->group(function () {
        Route::get('redirect', RedirectController::class)->name('redirect');
        Route::get('callback', CallbackController::class)->name('callback');
    });

// Webhook route - separate from authenticated routes
Route::match(['GET', 'POST'], config('strava-client.webhook.endpoint', '/strava/webhook'), WebhookController::class)
    ->name('strava.webhook');
