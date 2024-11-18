<?php

use Illuminate\Support\Facades\Route;
use JordanPartridge\StravaClient\Http\Controllers\CallBackController;
use JordanPartridge\StravaClient\Http\Controllers\RedirectController;

Route::prefix('strava')->as('strava:')->group(function () {
    Route::get('redirect', RedirectController::class)->name('redirect')->middleware('web');
    Route::get('callback', CallbackController::class)->name('callback');
});
