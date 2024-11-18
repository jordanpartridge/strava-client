<?php

namespace JordanPartridge\StravaClient\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JordanPartridge\StravaClient\Models\StravaToken;

/**
 * Provides Strava token relationship functionality.
 *
 * @mixin Model
 */
trait HasStravaTokens
{
    public function stravaToken(): HasOne
    {
        return $this->hasOne(StravaToken::class);
    }
}
