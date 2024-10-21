<?php

namespace JordanPartridge\StravaClient\Http\Integration\Strava\Requests;

use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use function PHPUnit\Framework\returnArgument;

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
        if ($id <= 0) {
            throw new InvalidArgumentException('Activity ID must be a positive integer.');
        }
        $this->id = $id;
    }

    public function resolveEndpoint(): string
    {
        return sprintf('/activities/%d', $this->id);
    }
}
