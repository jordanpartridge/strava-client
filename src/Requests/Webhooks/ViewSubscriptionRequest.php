<?php

namespace JordanPartridge\StravaClient\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ViewSubscriptionRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/push_subscriptions';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}