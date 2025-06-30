<?php

namespace JordanPartridge\StravaClient\Data\Webhooks;

use Carbon\Carbon;

readonly class SubscriptionData
{
    public function __construct(
        public int $id,
        public string $callback_url,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            callback_url: $data['callback_url'],
            created_at: Carbon::parse($data['created_at']),
            updated_at: Carbon::parse($data['updated_at']),
        );
    }
}