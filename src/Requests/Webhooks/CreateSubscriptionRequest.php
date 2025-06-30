<?php

namespace JordanPartridge\StravaClient\Requests\Webhooks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateSubscriptionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private string $callbackUrl,
        private string $verifyToken,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/push_subscriptions';
    }

    protected function defaultBody(): array
    {
        return [
            'callback_url' => $this->callbackUrl,
            'verify_token' => $this->verifyToken,
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}