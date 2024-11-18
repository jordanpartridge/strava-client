<?php

namespace JordanPartridge\StravaClient\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RedirectController
{

    public function __invoke(Request $request)
    {
        $state = Str::random(16);

        // Store the state data with the short key
        $stateData = [
            'user_id'   => $request->user()->getAuthIdentifier(),
            'timestamp' => now()->timestamp,
        ];

        Cache::put('strava_state:' . $state, $stateData, now()->addMinutes(10));

        $query = http_build_query([
            'client_id'     => config('strava-client.client_id'),
            'redirect_uri'  => route('strava:callback'),
            'response_type' => 'code',
            'scope'         => config('strava-client.scope'),
            'state'         => $state,  // Add this for security
        ]);

        return redirect(config('services.strava.authorize_url') . '?' . $query);
    }

}
