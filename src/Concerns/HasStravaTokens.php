<?php

namespace JordanPartridge\StravaClient\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;
use JordanPartridge\StravaClient\Models\StravaToken;

trait HasStravaTokens
{
    public function stravaToken(): HasOne
    {
        return $this->hasOne(StravaToken::class);
    }
}
