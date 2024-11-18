<?php

namespace JordanPartridge\StravaClient\Models;

use Illuminate\Database\Eloquent\Model;

class StravaToken extends Model
{
    /**
     * @var array<int,string>
     */
    protected $fillable = ['access_token', 'athlete_id', 'refresh_token', 'user_id', 'expires_at'];

    /**
     * @var array <string, string>
     */
    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at' => 'datetime',
    ];
}
