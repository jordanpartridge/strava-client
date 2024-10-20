<?php

namespace App\Http\Integrations\Strava\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ActivityRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The ID of the activity
     */
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return '/activities/' . $this->id;
    }
}
