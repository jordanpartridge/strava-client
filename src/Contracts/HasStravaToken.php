<?php

namespace JordanPartridge\StravaClient\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

interface HasStravaToken
{
    public function stravaToken(): HasOne;
}
