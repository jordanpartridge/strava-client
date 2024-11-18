<?php

namespace JordanPartridge\StravaClient\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class RedirectController
{
    public function __invoke(Request $request)
    {
        if (empty(config('strava-client.client_id'))) {
            throw new RuntimeException('Strava client ID is not configured');
        }
        /**
         * For more `entropy`
         */
        $state = Str::random(32);

        // Store the state data with the short key
        $stateData = [
            'user_id' => $request->user()->getAuthIdentifier(),
            'timestamp' => now()->timestamp,
        ];
        try {
            Cache::put('strava_state:'.$state, $stateData, now()->addMinutes(10));

        } catch (Exception $e) {
            Log::error('Failed to store Strava state', ['error' => $e->getMessage()]);
            throw new RuntimeException('Authentication flow initialization failed');
        }

        $query = http_build_query([
            'client_id' => config('strava-client.client_id'),
            'redirect_uri' => route('strava:callback'),
            'response_type' => 'code',
            'scope' => config('strava-client.scope'),
            'state' => $state,
        ]);

        return redirect(config('strava-client.authorize_url').'?'.$query);
    }
}
