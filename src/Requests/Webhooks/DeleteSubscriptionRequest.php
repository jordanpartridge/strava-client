<?php

namespace JordanPartridge\StravaClient\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteSubscriptionRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(private int $subscriptionId) {}

    public function resolveEndpoint(): string
    {
        return "/push_subscriptions/{$this->subscriptionId}";
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
