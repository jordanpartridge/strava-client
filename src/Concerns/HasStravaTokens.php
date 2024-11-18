<?php

namespace JordanPartridge\StravaClient\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasStravaTokens
{
    public function stravaToken(): HasOne
    {
        return $this->hasOne(StravaToken::class);
    }
}
