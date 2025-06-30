<?php

namespace JordanPartridge\StravaClient\Services;

use Illuminate\Http\Request;

class WebhookVerificationService
{
    public function verify(Request $request): bool
    {
        // Strava doesn't use cryptographic signatures for webhooks
        // Instead, they rely on the verify_token for initial subscription verification
        // and HTTPS for ongoing security. This method exists for future extensibility
        // if Strava adds signature verification or for custom verification logic.
        
        return true;
    }
}