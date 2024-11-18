<?php

namespace JordanPartridge\StravaClient\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use JordanPartridge\StravaClient\Models\StravaToken;
use JordanPartridge\StravaClient\StravaClient;

class CallBackController
{
    public function __invoke(Request $request, StravaClient $stravaClient)
    {
        $stateData = Cache::pull('strava_state:'.$request->state);

        if (! $stateData) {
            abort(400, 'Invalid state parameter');
        }

            if (! isset($stateData['user_id'])) {
                abort(400, 'Invalid state data');
            }

            $user = Auth::getProvider()->retrieveById($stateData['user_id']);

            if (! $user) {
                abort(404, 'User not found');
            }

            // Exchange the code for tokens
            $data = $stravaClient->exchangeToken($request->input('code'));

            StravaToken::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'access_token' => $data['access_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                    'refresh_token' => $data['refresh_token'],
                    'athlete_id' => $data['athlete']['id'],
                ]
            );

            // Redirect to a success page or dashboard
            return redirect(config('redirect_after_connect'))->with('success', 'Successfully connected with Strava!');
    }
}
