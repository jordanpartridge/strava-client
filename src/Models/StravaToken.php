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
     * @param $value
     * @return void
     */
    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = encrypt($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getAccessTokenAttribute($value): mixed
    {
        return decrypt($value);
    }
}
