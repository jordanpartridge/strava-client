<?php

use Illuminate\Support\Facades\Route;
use JordanPartridge\StravaClient\Http\Controllers\CallBackController;
use JordanPartridge\StravaClient\Http\Controllers\RedirectController;

Route::prefix('strava')
    ->as('strava:')
    ->middleware(['web', config('auth.defaults.guard')])->group(function () {
        Route::get('redirect', RedirectController::class)->name('redirect');
        Route::get('callback', CallbackController::class)->name('callback');
    });
